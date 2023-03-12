<?php

use Illuminate\Support\Facades\Route;
use Koellich\Persources\Facades\Persources;
use Koellich\Persources\Http\PersourcesController;

Route::middleware(config('persources.middleware_group'))
    ->name('persources.')
    ->group(function () {
        foreach (Persources::getResources() as $resource) {
            foreach ($resource->getPermissions() as $permission) {
                $action = Persources::getAction($permission);
                $route = trim(config('persources.route_root').'/'.$resource->pluralName, '/');
                if (in_array($action, ['view', 'update', 'delete'])) {
                    $route .= '/{id}';
                }
                $method = match ($action) {
                    'list', 'view' => 'GET',
                    'create' => 'POST',
                    'update' => 'PATCH',
                    'delete' => 'DELETE'
                };
                Route::addRoute($method, $route, PersourcesController::class)->name($permission);
            }
        }
    });
