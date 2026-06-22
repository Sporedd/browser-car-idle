<?php

namespace App\Services;

use App\Enums\CarModel;
use App\Enums\FactoryStatus;
use App\Enums\LocationTier;
use App\Enums\ResourceType;
use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use App\Models\FactoryLine;
use App\Models\GameState;
use App\Models\ResourceNode;
use App\Models\Showroom;
use App\Models\TransportVehicle;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GameSetupService
{
    public function setup(User $user): GameState
    {
        return DB::transaction(function () use ($user): GameState {
            $gameState = GameState::create([
                'user_id' => $user->id,
                'money' => config('game.starting_money'),
                'last_ticked_at' => now(),
            ]);

            ResourceNode::create([
                'game_state_id' => $gameState->id,
                'type' => ResourceType::Steel,
                'speed_level' => 1,
                'storage_level' => 1,
                'amount' => 50,
            ]);

            FactoryLine::create([
                'game_state_id' => $gameState->id,
                'name' => 'Line 1',
                'car_model' => CarModel::Economy,
                'status' => FactoryStatus::Idle,
                'queue_count' => 0,
                'speed_level' => 1,
                'quality_mode' => false,
                'worker_count' => 3,
                'robot_count' => 0,
                'progress_seconds' => 0,
                'completed_buffer' => 0,
            ]);

            $showroom = Showroom::create([
                'game_state_id' => $gameState->id,
                'name' => 'Downtown Dealership',
                'location_tier' => LocationTier::Neighborhood,
                'staff_level' => 1,
                'marketing_level' => 1,
            ]);

            TransportVehicle::create([
                'game_state_id' => $gameState->id,
                'vehicle_type' => VehicleType::SmallTruck,
                'status' => VehicleStatus::Idle,
                'capacity_level' => 1,
                'speed_level' => 1,
                'assigned_showroom_id' => $showroom->id,
                'cargo_count' => 0,
                'cargo_model' => null,
            ]);

            return $gameState;
        });
    }
}
