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
                    // from a permission something.car.read we generate the route <persources.route_root>/something/car
                    $route = str_replace('.', '/', str_replace($action, '', $permission));
                    $route = strtolower(trim(config('persources.route_root').'/'.$route, '/'));
                    if (in_array($impliedAction, ['view', 'update', 'delete'])) {
                        $route .= '/{id}';
                    }
                    $method = Persources::getHttpMethod($impliedAction);
                    $routeName = Persources::getRouteName($permission, $impliedAction);
                    Route::addRoute($method, $route, PersourcesController::class)->name($routeName);
                }
            }
        }
    });
