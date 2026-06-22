<?php

namespace App\Models;

use App\Enums\CarModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $showroom_id
 * @property CarModel $car_model
 * @property int $quantity
 * @property string|null $price_override
 */
class ShowroomInventory extends Model
{
    protected $table = 'showroom_inventory';

    protected $fillable = [
        'showroom_id',
        'car_model',
        'quantity',
        'price_override',
    ];

    protected function casts(): array
    {
        return [
            'car_model' => CarModel::class,
            'quantity' => 'integer',
            'price_override' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Showroom, $this> */
    public function showroom(): BelongsTo
    {
        return $this->belongsTo(Showroom::class);
    }

    public function effectivePrice(): float
    {
        if ($this->price_override !== null) {
            return (float) $this->price_override;
        }

        return config("game.car_models.{$this->car_model->value}.base_sell_price");
    }

    public function salesRatePerMinute(Showroom $showroom): float
    {
        $config = config("game.car_models.{$this->car_model->value}");
        $basePrice = $config['base_sell_price'];
        $priceRatio = $this->effectivePrice() / $basePrice;
        $priceMultiplier = max(0.1, 1 / $priceRatio);

        return $config['base_demand_per_minute'] * $showroom->demandMultiplier() * $priceMultiplier;
    }
}
