<?php

return [

    'enabled' => true,

    'list' => [
        [
            'min_point' => 100,
            'use_point' => 150,
            'discount' => 5, // diskon 5%
            'description' => 'Diskon 5% untuk member dengan poin > 100'
        ],
        [
            'min_point' => 200,
            'use_point' => 270,
            'discount' => 10, // diskon 10%
            'description' => 'Diskon 10% untuk member dengan poin > 200'
        ]
    ]
];
