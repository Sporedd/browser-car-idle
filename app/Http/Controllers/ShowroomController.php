<?php

namespace App\Http\Controllers;

use App\Models\GameState;
use App\Models\Showroom;
use App\Models\ShowroomInventory;
use App\Services\GameTickService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShowroomController extends Controller
{
    public function __construct(private readonly GameTickService $tickService) {}

    public function setPrice(Request $request, ShowroomInventory $showroomInventory): RedirectResponse
    {
        $gameState = $this->authorizeAndTick($request, $showroomInventory->showroom->game_state_id);

        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:1', 'max:999999'],
        ]);

        $showroomInventory->update(['price_override' => $validated['price']]);

        return back(302, [], route('game'));
    }

    public function resetPrice(Request $request, ShowroomInventory $showroomInventory): RedirectResponse
    {
        $this->authorizeAndTick($request, $showroomInventory->showroom->game_state_id);

        $showroomInventory->update(['price_override' => null]);

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
