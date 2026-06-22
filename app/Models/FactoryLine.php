<?php

namespace App\Models;

use App\Enums\CarModel;
use App\Enums\FactoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $game_state_id
 * @property string $name
 * @property CarModel $car_model
 * @property FactoryStatus $status
 * @property int $queue_count
 * @property int $speed_level
 * @property bool $quality_mode
 * @property int $worker_count
 * @property int $robot_count
 * @property string|float $progress_seconds
 * @property int $completed_buffer
 */
class FactoryLine extends Model
{
    protected $fillable = [
        'game_state_id',
        'name',
        'car_model',
        'status',
        'queue_count',
        'speed_level',
        'quality_mode',
        'worker_count',
        'robot_count',
        'progress_seconds',
        'completed_buffer',
    ];

    protected function casts(): array
    {
        return [
            'car_model' => CarModel::class,
            'status' => FactoryStatus::class,
            'queue_count' => 'integer',
            'speed_level' => 'integer',
            'quality_mode' => 'boolean',
            'worker_count' => 'integer',
            'robot_count' => 'integer',
            'progress_seconds' => 'decimal:4',
            'completed_buffer' => 'integer',
        ];
    }

    /** @return BelongsTo<GameState, $this> */
    public function gameState(): BelongsTo
    {
        return $this->belongsTo(GameState::class);
    }

    public function productionTimeSeconds(): float
    {
        $config = config("game.car_models.{$this->car_model->value}");
        $time = $config['base_production_seconds'] * pow($config['speed_level_multiplier'], $this->speed_level - 1);

        return $this->quality_mode ? $time * $config['quality_time_multiplier'] : $time;
    }

    public function progressPercent(): float
    {
        $productionTime = $this->productionTimeSeconds();

        if ($productionTime <= 0) {
            return 0;
        }

        return min(100, ((float) $this->progress_seconds / $productionTime) * 100);
    }

    public function upgradeSpeedCost(): float
    {
        $config = config("game.car_models.{$this->car_model->value}");

        return $config['upgrade_speed_base_cost'] * pow($config['upgrade_speed_cost_multiplier'], $this->speed_level - 1);
    }

    /** @return array<string, int> */
    public function materialsRequired(): array
    {
        return config("game.car_models.{$this->car_model->value}.materials");
    }
}
