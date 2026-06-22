<?php

return [
    'starting_money' => 1000.00,

    'resources' => [
        'steel' => [
            'label' => 'Steel',
            'base_production_rate' => 2.0,
            'speed_multiplier' => 1.5,
            'base_capacity' => 100,
            'storage_multiplier' => 2.0,
            'upgrade_speed_base_cost' => 100,
            'upgrade_speed_cost_multiplier' => 2.0,
            'upgrade_storage_base_cost' => 150,
            'upgrade_storage_cost_multiplier' => 1.8,
        ],
    ],

    'car_models' => [
        'economy' => [
            'label' => 'Economy Car',
            'materials' => ['steel' => 5],
            'base_production_seconds' => 30,
            'speed_level_multiplier' => 0.8,
            'quality_time_multiplier' => 1.5,
            'quality_price_multiplier' => 1.25,
            'base_sell_price' => 500,
            'base_demand_per_minute' => 1.5,
            'upgrade_speed_base_cost' => 200,
            'upgrade_speed_cost_multiplier' => 2.5,
        ],
    ],

    'transport' => [
        'small_truck' => [
            'label' => 'Small Truck',
            'base_capacity' => 5,
            'capacity_per_level' => 3,
            'base_transit_seconds' => 60,
            'speed_level_multiplier' => 0.8,
            'upgrade_capacity_base_cost' => 300,
            'upgrade_capacity_cost_multiplier' => 2.0,
            'upgrade_speed_base_cost' => 250,
            'upgrade_speed_cost_multiplier' => 2.0,
        ],
    ],

    'showrooms' => [
        'neighborhood' => [
            'label' => 'Neighborhood',
            'demand_multiplier' => 1.0,
        ],
        'city' => [
            'label' => 'City Center',
            'demand_multiplier' => 2.5,
        ],
        'metropolis' => [
            'label' => 'Metropolis',
            'demand_multiplier' => 5.0,
        ],
    ],

    'upgrades' => [
        'showroom_staff' => [
            'base_cost' => 400,
            'cost_multiplier' => 2.2,
            'level_multiplier' => 1.3,
        ],
        'showroom_marketing' => [
            'base_cost' => 350,
            'cost_multiplier' => 2.0,
            'level_multiplier' => 1.2,
        ],
    ],
];
