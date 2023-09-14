<?php

namespace Koellich\Persources\Http;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Koellich\Persources\Facades\Persources;

class PersourcesController extends Controller
{
    public function __invoke(Request $request)
    {
        // the route name is "persources.PERMISSION|action
        // so it could be persources.cars.read|list, persources.something.cars.read|view, persources.xyz.cars.list|list
        $routeName = Str::of($request->route()->getName());
        $permission = str_replace('persources.', '', $routeName->beforeLast('|'));
        if (! $permission) {
            abort(500, __('persources::translations.500_unnamed_route', ['route' => $request->path()]));
        }

        if (! Auth::user()) {
            return redirect('/');
        }

        if (! Persources::checkPermission($permission)) {
            abort(403, __('persources::translations.403_no_permission', ['permission' => $permission]));
        }

        $resource = Persources::getResourceForPermission($permission);
        if (! $resource) {
            abort(500, __('persources::translations.500_no_resource', ['permission' => $permission]));
        }

        $action = $routeName->afterLast('|')->toString();
        $action = $action ?: Persources::getAction($permission);
        $id = $request->route('id');

        return match ($action) {
            'list' => $resource->list(),
            'view' => $resource->view($id),
            'create' => $resource->create($request->all()),
            'update' => $resource->update($id, $request->all()),
            'delete' => $resource->delete($id)
        };
    }
}
