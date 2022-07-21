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
    | Table Prefix
    |--------------------------------------------------------------------------
    |
    | This option will add prefix in the tables which will be 
    | migrated using migration.
    |
    */
    'table_prefix' => '',

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



];