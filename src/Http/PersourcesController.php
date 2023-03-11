<?php

namespace Koellich\Persources\Http;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Koellich\Persources\Facades\Persources;

class PersourcesController extends Controller
{
    public function __invoke(Request $request)
    {
        $permission = str_replace("persources.", "", $request->route()->getName());
        if (!$permission) {
            abort(500, __("laravel-persources::translations.500_unnamed_route", ["route" => $request->path()]));
        }
        if (!Auth::user()->can($permission)) {
            abort(403, __("laravel-persources::translations.403_no_permission", ["permission" => $permission]));
        }

        $resource = Persources::getResourceFor($permission);
        if (!$resource) {
            abort(500, __("laravel-persources::translations.500_no_resource", ["permission" => $permission]));
        }

        $action = $this->getAction($permission);
        $id = $request->route("id");

        return match($action) {
            "list" => $resource->list($request),
            "view" => $resource->view($request, $id),
            "create" => $resource->create($request),
            "update" => $resource->update($request, $id),
            "delete" => $resource->delete($request, $id)
        };
    }
}