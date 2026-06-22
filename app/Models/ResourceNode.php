<?php

namespace App\Models;

use App\Enums\ResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $game_state_id
 * @property ResourceType $type
 * @property int $speed_level
 * @property int $storage_level
 * @property string|float $amount
 */
class ResourceNode extends Model
{
    protected $fillable = [
        'game_state_id',
        'type',
        'speed_level',
        'storage_level',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'type' => ResourceType::class,
            'speed_level' => 'integer',
            'storage_level' => 'integer',
            'amount' => 'decimal:4',
        ];
    }

    /** @return BelongsTo<GameState, $this> */
    public function gameState(): BelongsTo
    {
        return $this->belongsTo(GameState::class);
    }

    public function productionRate(): float
    {
        $config = config("game.resources.{$this->type->value}");

        return $config['base_production_rate'] * pow($config['speed_multiplier'], $this->speed_level - 1);
    }

    public function capacity(): float
    {
        $config = config("game.resources.{$this->type->value}");

        return $config['base_capacity'] * pow($config['storage_multiplier'], $this->storage_level - 1);
    }

    public function upgradeSpeedCost(): float
    {
        $config = config("game.resources.{$this->type->value}");

        return $config['upgrade_speed_base_cost'] * pow($config['upgrade_speed_cost_multiplier'], $this->speed_level - 1);
    }

    public function upgradeStorageCost(): float
    {
        $config = config("game.resources.{$this->type->value}");

        return $config['upgrade_storage_base_cost'] * pow($config['upgrade_storage_cost_multiplier'], $this->storage_level - 1);
    }
}
