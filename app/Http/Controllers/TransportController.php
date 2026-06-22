<?php

namespace App\Http\Controllers;

use App\Enums\VehicleStatus;
use App\Models\GameState;
use App\Models\TransportVehicle;
use App\Services\GameTickService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransportController extends Controller
{
    public function __construct(private readonly GameTickService $tickService) {}

    public function dispatch(Request $request, TransportVehicle $transportVehicle): RedirectResponse
    {
        $gameState = $this->authorizeAndTick($request, $transportVehicle->game_state_id);

        abort_if($transportVehicle->status !== VehicleStatus::Idle, 422, 'Vehicle is not idle.');
        abort_if($transportVehicle->assigned_showroom_id === null, 422, 'No showroom assigned.');

        $factoryLine = $gameState->factoryLines->first();
        abort_if($factoryLine === null || $factoryLine->completed_buffer === 0, 422, 'No cars to dispatch.');

        $capacity = $transportVehicle->capacity();
        $toLoad = min($factoryLine->completed_buffer, $capacity);
        $carModel = $factoryLine->car_model;

        $transitTime = (int) $transportVehicle->transitTimeSeconds();

        $factoryLine->completed_buffer -= $toLoad;
        $factoryLine->save();

        $transportVehicle->status = VehicleStatus::InTransit;
        $transportVehicle->cargo_count = $toLoad;
        $transportVehicle->cargo_model = $carModel;
        $transportVehicle->arrives_at = Carbon::now()->addSeconds($transitTime);
        $transportVehicle->returns_at = null;
        $transportVehicle->save();

        return to_route('game');
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

        return $gameState->refresh()->load([
            'resourceNodes',
            'factoryLines',
            'transportVehicles.assignedShowroom',
            'showrooms.inventory',
        ]);
    }
}
