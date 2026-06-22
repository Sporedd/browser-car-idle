<?php

namespace App\Http\Controllers;

use App\Enums\FactoryStatus;
use App\Models\FactoryLine;
use App\Models\GameState;
use App\Models\ResourceNode;
use App\Services\GameTickService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FactoryController extends Controller
{
    public function __construct(private readonly GameTickService $tickService) {}

    public function queue(Request $request, FactoryLine $factoryLine): RedirectResponse
    {
        $validated = $request->validate([
            'count' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $count = $validated['count'];
        $gameState = $this->authorizeAndTick($request, $factoryLine->game_state_id);

        $materials = $factoryLine->materialsRequired();

        DB::transaction(function () use ($gameState, $factoryLine, $materials, $count): void {
            foreach ($materials as $type => $amountPerCar) {
                $totalRequired = $amountPerCar * $count;
                $node = ResourceNode::where('game_state_id', $gameState->id)
                    ->where('type', $type)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_if((float) $node->amount < $totalRequired, 422, "Not enough {$type}.");
                $node->decrement('amount', $totalRequired);
            }

            $factoryLine->queue_count += $count;

            if ($factoryLine->status === FactoryStatus::Idle) {
                $factoryLine->status = FactoryStatus::Running;
            }

            $factoryLine->save();
        });

        return back(302, [], route('game'));
    }

    public function toggleQuality(Request $request, FactoryLine $factoryLine): RedirectResponse
    {
        $this->authorizeAndTick($request, $factoryLine->game_state_id);

        $factoryLine->update(['quality_mode' => ! $factoryLine->quality_mode]);

        return back(302, [], route('game'));
    }

    private function authorizeAndTick(Request $request, int $gameStateId): GameState
    {
        $gameState = GameState::findOrFail($gameStateId);
        abort_if($gameState->user_id !== $request->user()->id, 403);

        $this->tickService->tick($gameState->load([
            'resourceNodes',
            'factoryLines',
            'transportVehicles.assignedShowroom',
            'showrooms.inventory',
        ]));

        return $gameState->refresh();
    }
}
