<?php

namespace App\Http\Resources;

use App\Models\FactoryLine;
use App\Models\GameState;
use App\Models\ResourceNode;
use App\Models\Showroom;
use App\Models\ShowroomInventory;
use App\Models\TransportVehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin GameState */
class GameStateResource extends JsonResource
{
    public static $wrap = null;
    public function toArray(Request $request): array
    {
        /** @var GameState $state */
        $state = $this->resource;

        return [
            'money' => (float) $state->money,
            'lastTickedAt' => $state->last_ticked_at->toIso8601String(),
            'resourceNodes' => $state->resourceNodes->map(fn (ResourceNode $node) => [
                'id' => $node->id,
                'type' => $node->type->value,
                'label' => config("game.resources.{$node->type->value}.label"),
                'amount' => (float) $node->amount,
                'capacity' => $node->capacity(),
                'productionRate' => $node->productionRate(),
                'speedLevel' => $node->speed_level,
                'storageLevel' => $node->storage_level,
                'upgradeSpeedCost' => $node->upgradeSpeedCost(),
                'upgradeStorageCost' => $node->upgradeStorageCost(),
            ])->values(),
            'factoryLines' => $state->factoryLines->map(fn (FactoryLine $line) => [
                'id' => $line->id,
                'name' => $line->name,
                'carModel' => $line->car_model->value,
                'carModelLabel' => config("game.car_models.{$line->car_model->value}.label"),
                'status' => $line->status->value,
                'queueCount' => $line->queue_count,
                'completedBuffer' => $line->completed_buffer,
                'speedLevel' => $line->speed_level,
                'qualityMode' => $line->quality_mode,
                'progressPercent' => $line->progressPercent(),
                'productionTimeSeconds' => $line->productionTimeSeconds(),
                'upgradeSpeedCost' => $line->upgradeSpeedCost(),
                'materialsRequired' => $line->materialsRequired(),
            ])->values(),
            'transportVehicles' => $state->transportVehicles->map(fn (TransportVehicle $vehicle) => [
                'id' => $vehicle->id,
                'vehicleType' => $vehicle->vehicle_type->value,
                'label' => config("game.transport.{$vehicle->vehicle_type->value}.label"),
                'status' => $vehicle->status->value,
                'cargoCount' => $vehicle->cargo_count,
                'cargoModel' => $vehicle->cargo_model?->value,
                'capacity' => $vehicle->capacity(),
                'speedLevel' => $vehicle->speed_level,
                'capacityLevel' => $vehicle->capacity_level,
                'assignedShowroomId' => $vehicle->assigned_showroom_id,
                'arrivesAt' => $vehicle->arrives_at?->toIso8601String(),
                'returnsAt' => $vehicle->returns_at?->toIso8601String(),
                'transitTimeSeconds' => $vehicle->transitTimeSeconds(),
                'upgradeSpeedCost' => $vehicle->upgradeSpeedCost(),
                'upgradeCapacityCost' => $vehicle->upgradeCapacityCost(),
            ])->values(),
            'showrooms' => $state->showrooms->map(fn (Showroom $showroom) => [
                'id' => $showroom->id,
                'name' => $showroom->name,
                'locationTier' => $showroom->location_tier->value,
                'staffLevel' => $showroom->staff_level,
                'marketingLevel' => $showroom->marketing_level,
                'demandMultiplier' => $showroom->demandMultiplier(),
                'upgradeStaffCost' => $showroom->upgradeStaffCost(),
                'upgradeMarketingCost' => $showroom->upgradeMarketingCost(),
                'inventory' => $showroom->inventory->map(fn (ShowroomInventory $inv) => [
                    'id' => $inv->id,
                    'carModel' => $inv->car_model->value,
                    'carModelLabel' => config("game.car_models.{$inv->car_model->value}.label"),
                    'quantity' => $inv->quantity,
                    'priceOverride' => $inv->price_override !== null ? (float) $inv->price_override : null,
                    'basePrice' => (float) config("game.car_models.{$inv->car_model->value}.base_sell_price"),
                    'effectivePrice' => $inv->effectivePrice(),
                    'salesRatePerMinute' => $inv->salesRatePerMinute($showroom),
                ])->values()->all(),
            ])->values(),
        ];
    }
}
