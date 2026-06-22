<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\CarbonInterface;

/**
 * @property int $id
 * @property int $user_id
 * @property string|float $money
 * @property CarbonInterface $last_ticked_at
 * @property int $prestige_level
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 */
class GameState extends Model
{
    protected $fillable = [
        'user_id',
        'money',
        'last_ticked_at',
        'prestige_level',
    ];

    protected function casts(): array
    {
        return [
            'money' => 'decimal:2',
            'last_ticked_at' => 'datetime',
            'prestige_level' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<ResourceNode, $this> */
    public function resourceNodes(): HasMany
    {
        return $this->hasMany(ResourceNode::class);
    }

    /** @return HasMany<FactoryLine, $this> */
    public function factoryLines(): HasMany
    {
        return $this->hasMany(FactoryLine::class);
    }

    /** @return HasMany<TransportVehicle, $this> */
    public function transportVehicles(): HasMany
    {
        return $this->hasMany(TransportVehicle::class);
    }

    /** @return HasMany<Showroom, $this> */
    public function showrooms(): HasMany
    {
        return $this->hasMany(Showroom::class);
    }
}
