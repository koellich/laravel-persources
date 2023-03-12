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
                $impliedActions = Persources::getImpliedActions($action);
                foreach ($impliedActions as $impliedAction) {
                    $route = strtolower(trim(config('persources.route_root').'/'.$resource->pluralName, '/'));
                    if (in_array($impliedAction, ['view', 'update', 'delete'])) {
                        $route .= '/{id}';
                    }
                    $method = match ($impliedAction) {
                        'list', 'view' => 'GET',
                        'create' => 'POST',
                        'update' => 'PATCH',
                        'delete' => 'DELETE'
                    };
                    $routeName = Persources::getRouteName($permission, $impliedAction);
                    Route::addRoute($method, $route, PersourcesController::class)->name($routeName);
                }
            }
        }
    });
