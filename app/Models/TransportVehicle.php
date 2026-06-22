<?php

namespace App\Models;

use App\Enums\CarModel;
use App\Enums\VehicleStatus;
use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\CarbonInterface;

/**
 * @property int $id
 * @property int $game_state_id
 * @property VehicleType $vehicle_type
 * @property VehicleStatus $status
 * @property int $capacity_level
 * @property int $speed_level
 * @property int|null $assigned_showroom_id
 * @property int $cargo_count
 * @property CarModel|null $cargo_model
 * @property CarbonInterface|null $arrives_at
 * @property CarbonInterface|null $returns_at
 */
class TransportVehicle extends Model
{
    protected $fillable = [
        'game_state_id',
        'vehicle_type',
        'status',
        'capacity_level',
        'speed_level',
        'assigned_showroom_id',
        'cargo_count',
        'cargo_model',
        'arrives_at',
        'returns_at',
    ];

    protected function casts(): array
    {
        return [
            'vehicle_type' => VehicleType::class,
            'status' => VehicleStatus::class,
            'cargo_model' => CarModel::class,
            'capacity_level' => 'integer',
            'speed_level' => 'integer',
            'cargo_count' => 'integer',
            'arrives_at' => 'datetime',
            'returns_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<GameState, $this> */
    public function gameState(): BelongsTo
    {
        return $this->belongsTo(GameState::class);
    }

    /** @return BelongsTo<Showroom, $this> */
    public function assignedShowroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class, 'assigned_showroom_id');
    }

    public function capacity(): int
    {
        $config = config("game.transport.{$this->vehicle_type->value}");

        return $config['base_capacity'] + ($config['capacity_per_level'] * ($this->capacity_level - 1));
    }

    public function transitTimeSeconds(): float
    {
        $config = config("game.transport.{$this->vehicle_type->value}");

        return $config['base_transit_seconds'] * pow($config['speed_level_multiplier'], $this->speed_level - 1);
    }

    public function upgradeCapacityCost(): float
    {
        $config = config("game.transport.{$this->vehicle_type->value}");

        return $config['upgrade_capacity_base_cost'] * pow($config['upgrade_capacity_cost_multiplier'], $this->capacity_level - 1);
    }

    public function upgradeSpeedCost(): float
    {
        $config = config("game.transport.{$this->vehicle_type->value}");

        return $config['upgrade_speed_base_cost'] * pow($config['upgrade_speed_cost_multiplier'], $this->speed_level - 1);
    }
}
