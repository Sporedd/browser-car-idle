<?php

namespace App\Http\Controllers;

use App\Http\Resources\GameStateResource;
use App\Models\GameState;
use App\Services\GameSetupService;
use App\Services\GameTickService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    public function __construct(
        private readonly GameSetupService $setupService,
        private readonly GameTickService $tickService,
    ) {}

    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $gameState = GameState::with([
            'resourceNodes',
            'factoryLines',
            'transportVehicles.assignedShowroom',
            'showrooms.inventory',
        ])->where('user_id', $user->id)->first();

        if ($gameState === null) {
            $gameState = $this->setupService->setup($user);
            $gameState->load([
                'resourceNodes',
                'factoryLines',
                'transportVehicles.assignedShowroom',
                'showrooms.inventory',
            ]);
        }

        $this->tickService->tick($gameState);

        $gameState->refresh()->load([
            'resourceNodes',
            'factoryLines',
            'transportVehicles.assignedShowroom',
            'showrooms.inventory',
        ]);

        $tab = $request->query('tab', 'resources');
        $allowedTabs = ['resources', 'factory', 'transport', 'showrooms'];
        $activeTab = is_string($tab) && in_array($tab, $allowedTabs, true) ? $tab : 'resources';

        return Inertia::render('game', [
            'gameState' => GameStateResource::make($gameState),
            'activeTab' => $activeTab,
        ]);
    }
}
