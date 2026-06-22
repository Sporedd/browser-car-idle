<?php

namespace App\Http\Controllers;

use App\Models\FactoryLine;
use App\Models\GameState;
use App\Models\ResourceNode;
use App\Models\Showroom;
use App\Models\TransportVehicle;
use App\Services\GameTickService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UpgradeController extends Controller
{
    public function __construct(private readonly GameTickService $tickService) {}

    public function mineSpeed(Request $request, ResourceNode $resourceNode): RedirectResponse
    {
        $gameState = $this->authorizeGameState($request, $resourceNode->game_state_id);
        $cost = $resourceNode->upgradeSpeedCost();

        $this->deductMoney($gameState, $cost);
        $resourceNode->increment('speed_level');

        return to_route('game');
    }

    public function mineStorage(Request $request, ResourceNode $resourceNode): RedirectResponse
    {
        $gameState = $this->authorizeGameState($request, $resourceNode->game_state_id);
        $cost = $resourceNode->upgradeStorageCost();

        $this->deductMoney($gameState, $cost);
        $resourceNode->increment('storage_level');

        return to_route('game');
    }

    public function factorySpeed(Request $request, FactoryLine $factoryLine): RedirectResponse
    {
        $gameState = $this->authorizeGameState($request, $factoryLine->game_state_id);
        $cost = $factoryLine->upgradeSpeedCost();

        $this->deductMoney($gameState, $cost);
        $factoryLine->increment('speed_level');

        return to_route('game');
    }

    public function transportCapacity(Request $request, TransportVehicle $transportVehicle): RedirectResponse
    {
        $gameState = $this->authorizeGameState($request, $transportVehicle->game_state_id);
        $cost = $transportVehicle->upgradeCapacityCost();

        $this->deductMoney($gameState, $cost);
        $transportVehicle->increment('capacity_level');

        return to_route('game');
    }

    public function transportSpeed(Request $request, TransportVehicle $transportVehicle): RedirectResponse
    {
        $gameState = $this->authorizeGameState($request, $transportVehicle->game_state_id);
        $cost = $transportVehicle->upgradeSpeedCost();

        $this->deductMoney($gameState, $cost);
        $transportVehicle->increment('speed_level');

        return to_route('game');
    }

    public function showroomStaff(Request $request, Showroom $showroom): RedirectResponse
    {
        $gameState = $this->authorizeGameState($request, $showroom->game_state_id);
        $cost = $showroom->upgradeStaffCost();

        $this->deductMoney($gameState, $cost);
        $showroom->increment('staff_level');

        return to_route('game');
    }

    public function showroomMarketing(Request $request, Showroom $showroom): RedirectResponse
    {
        $gameState = $this->authorizeGameState($request, $showroom->game_state_id);
        $cost = $showroom->upgradeMarketingCost();

        $this->deductMoney($gameState, $cost);
        $showroom->increment('marketing_level');

        return to_route('game');
    }

    private function authorizeGameState(Request $request, int $gameStateId): GameState
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

    private function deductMoney(GameState $gameState, float $cost): void
    {
        abort_if((float) $gameState->money < $cost, 422, 'Not enough money.');
        $gameState->decrement('money', $cost);
    }
}
