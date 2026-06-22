<?php

namespace App\Models;

use App\Enums\LocationTier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $game_state_id
 * @property string $name
 * @property LocationTier $location_tier
 * @property int $staff_level
 * @property int $marketing_level
 */
class Showroom extends Model
{
    protected $fillable = [
        'game_state_id',
        'name',
        'location_tier',
        'staff_level',
        'marketing_level',
    ];

    protected function casts(): array
    {
        return [
            'location_tier' => LocationTier::class,
            'staff_level' => 'integer',
            'marketing_level' => 'integer',
        ];
    }

    /** @return BelongsTo<GameState, $this> */
    public function gameState(): BelongsTo
    {
        return $this->belongsTo(GameState::class);
    }

    /** @return HasMany<ShowroomInventory, $this> */
    public function inventory(): HasMany
    {
        return $this->hasMany(ShowroomInventory::class);
    }

    public function demandMultiplier(): float
    {
        $locationMultiplier = config("game.showrooms.{$this->location_tier->value}.demand_multiplier");
        $staffConfig = config('game.upgrades.showroom_staff');
        $marketingConfig = config('game.upgrades.showroom_marketing');

        $staffMultiplier = pow($staffConfig['level_multiplier'], $this->staff_level - 1);
        $marketingMultiplier = pow($marketingConfig['level_multiplier'], $this->marketing_level - 1);

        return (float) $locationMultiplier * $staffMultiplier * $marketingMultiplier;
    }

    public function upgradeStaffCost(): float
    {
        $config = config('game.upgrades.showroom_staff');

        return $config['base_cost'] * pow($config['cost_multiplier'], $this->staff_level - 1);
    }

    public function upgradeMarketingCost(): float
    {
        $config = config('game.upgrades.showroom_marketing');

        return $config['base_cost'] * pow($config['cost_multiplier'], $this->marketing_level - 1);
    }
}
