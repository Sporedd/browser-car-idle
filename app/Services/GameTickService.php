<?php

namespace App\Services;

use App\Enums\FactoryStatus;
use App\Enums\VehicleStatus;
use App\Models\FactoryLine;
use App\Models\GameState;
use App\Models\ResourceNode;
use App\Models\Showroom;
use App\Models\ShowroomInventory;
use App\Models\TransportVehicle;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class GameTickService
{
    public function tick(GameState $gameState): void
    {
        $now = now();
        $elapsed = (int) $now->diffInSeconds($gameState->last_ticked_at);

        if ($elapsed < 1) {
            return;
        }

        DB::transaction(function () use ($gameState, $elapsed, $now): void {
            $this->processResources($gameState, $elapsed);
            $this->processFactories($gameState, $elapsed);
            $this->processTransport($gameState, $now);
            $this->processSales($gameState, $elapsed);

            $gameState->last_ticked_at = $now;
            $gameState->save();
        });
    }

    private function processResources(GameState $gameState, int $elapsed): void
    {
        $gameState->resourceNodes->each(function (ResourceNode $node) use ($elapsed): void {
            $produced = $node->productionRate() * $elapsed;
            $node->amount = min((float) $node->amount + $produced, $node->capacity());
            $node->save();
        });
    }

    private function processFactories(GameState $gameState, int $elapsed): void
    {
        $gameState->factoryLines
            ->filter(fn (FactoryLine $line) => $line->status === FactoryStatus::Running)
            ->each(function (FactoryLine $line) use ($elapsed): void {
                $productionTime = $line->productionTimeSeconds();
                $line->progress_seconds = (float) $line->progress_seconds + $elapsed;

                $completed = 0;
                while ((float) $line->progress_seconds >= $productionTime && $line->queue_count > 0) {
                    $line->progress_seconds = (float) $line->progress_seconds - $productionTime;
                    $line->queue_count -= 1;
                    $completed++;
                }

                $line->completed_buffer += $completed;

                if ($line->queue_count === 0) {
                    $line->status = FactoryStatus::Idle;
                    $line->progress_seconds = 0;
                }

                $line->save();
            });
    }

    private function processTransport(GameState $gameState, CarbonInterface $now): void
    {
        $gameState->transportVehicles->each(function (TransportVehicle $vehicle) use ($now): void {
            if ($vehicle->status === VehicleStatus::InTransit && $vehicle->arrives_at?->lte($now)) {
                $this->deliverCargo($vehicle, $now);
            } elseif ($vehicle->status === VehicleStatus::Returning && $vehicle->returns_at?->lte($now)) {
                $vehicle->status = VehicleStatus::Idle;
                $vehicle->arrives_at = null;
                $vehicle->returns_at = null;
                $vehicle->save();
            }
        });
    }

    private function deliverCargo(TransportVehicle $vehicle, CarbonInterface $now): void
    {
        if ($vehicle->assignedShowroom && $vehicle->cargo_count > 0 && $vehicle->cargo_model !== null) {
            $inventory = ShowroomInventory::firstOrCreate(
                [
                    'showroom_id' => $vehicle->assigned_showroom_id,
                    'car_model' => $vehicle->cargo_model->value,
                ],
                ['quantity' => 0]
            );
            $inventory->increment('quantity', $vehicle->cargo_count);
        }

        $transitTime = (int) $vehicle->transitTimeSeconds();

        $vehicle->status = VehicleStatus::Returning;
        $vehicle->cargo_count = 0;
        $vehicle->cargo_model = null;
        $vehicle->returns_at = $now->copy()->addSeconds($transitTime);
        $vehicle->save();
    }

    private function processSales(GameState $gameState, int $elapsed): void
    {
        $totalRevenue = 0.0;

        $gameState->showrooms->each(function (Showroom $showroom) use ($elapsed, &$totalRevenue): void {
            $showroom->inventory->each(function (ShowroomInventory $inventory) use ($showroom, $elapsed, &$totalRevenue): void {
                if ($inventory->quantity <= 0) {
                    return;
                }

                $salesRatePerSecond = $inventory->salesRatePerMinute($showroom) / 60;
                $carsSold = min($inventory->quantity, (int) floor($salesRatePerSecond * $elapsed));

                if ($carsSold <= 0) {
                    return;
                }

                $totalRevenue += $carsSold * $inventory->effectivePrice();
                $inventory->quantity -= $carsSold;
                $inventory->save();
            });
        });

        if ($totalRevenue > 0) {
            $gameState->money = (float) $gameState->money + $totalRevenue;
            $gameState->save();
        }
    }
}
