<?php 

return [
    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This option will add prefix in routes for translations.
    |
    */
    'route_prefix' => 'translations',

    'route_middleware' => ['web','auth'],

    /*
    |--------------------------------------------------------------------------
    | Extend blade
    |--------------------------------------------------------------------------
    |
    | Give the name of blade file which will be extend. Default will be 
    | "layouts.app"
    |
    */
    'extend_blade' => 'layouts.app',

    'content_section' => 'content',

    'javascript_section' => 'js',

    'trans_functions' => [
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        'Lang::trans',
        'Lang::transChoice',
        '@lang',
        '@choice',
        '__',
        '$trans.get',
    ],

    'exclude_groups' => [],
];