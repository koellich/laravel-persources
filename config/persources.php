<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Namespace
    |--------------------------------------------------------------------------
    |
    | This is the namespace under which the Resources are generated.
    |
    */

    'resources_namespace' => env('PERSOURCES_RESOURCES_NAMESPACE', 'App\\Persources'),

    /*
    |--------------------------------------------------------------------------
    | Route Root Path
    |--------------------------------------------------------------------------
    |
    | This is the root path under which all persources routes will be generated.
    | Change this to avoid collisions with your other routes.
    |
    */

    'route_root' => env('PERSOURCES_ROOT', ''),

    /*
    |--------------------------------------------------------------------------
    | View Root Path
    |--------------------------------------------------------------------------
    |
    | This is the path under which the view templates are generated in the
    | resources path.
    |
    */

    'view_root' => env('PERSOURCES_VIEWS_ROOT', 'views/persources'),

    /*
    |--------------------------------------------------------------------------
    | Middleware Group
    |--------------------------------------------------------------------------
    |
    | This is the middleware group that is used for the generated routes
    |
    */

    'middleware_group' => env('PERSOURCES_MIDDLEWARE_GROUP', 'web'),

    /*
    |--------------------------------------------------------------------------
    | Admin Role
    |--------------------------------------------------------------------------
    |
    | This is the Role which can access all resources regardless of permissions.
    | Set this to null if you do not want to have an admin role.
    |
    */

    'admin_role' => env('PERSOURCES_ADMIN_PERMISSION', 'admin'),
];
