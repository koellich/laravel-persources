<?php

namespace Koellich\Persources;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;

class Resource
{
    /**
     * @var string Full qualified name of the Model that this resource represents.
     */
    public string $model;

    /**
     * @var string Singular Name of the resource.
     */
    public string $singularName;

    /**
     * @var string Plural name of the resource.
     */
    public string $pluralName;

    /**
     * @var array Array of permissions (string) that are handled by this resource.
     */
    public array $permissions = [];

    /**
     * @var array Array of model attributes that can be used when displaying a list of models.
     */
    public array $listItemAttributes = [];

    /**
     * @var array Array of model attributes that can be used when displaying a single model.
     */
    public array $singleItemAttributes = [];

    /**
     * @var array Array of actions that are available.
     */
    public array $actions = [];

    public function list(Request $request)
    {
        return view($this->getView('list'), ['resource' => $this, 'items' => $this->getItems($request)]);
    }

    public function view(Request $request, $id)
    {
        return view($this->getView('view'), ['resource' => $this, 'item' => $this->getItem($request, $id)]);
    }

    public function create(Request $request)
    {
        $ok = $this->getModelClassName()::create($request->all());

        return $ok ? Response::noContent() : abort(400);
    }

    public function update(Request $request, $id)
    {
        $ok = $this->getModelClassName()::find($id)->update($request->all());

        return $ok ? Response::noContent() : abort(400);
    }

    public function delete(Request $request, $id)
    {
        $ok = $this->getModelClassName()::find($id)->delete();

        return $ok ? Response::noContent() : abort(400);
    }

    /**
     * Returns all permissions that are handled by this resource.
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Returns a list of all Models that fit the request
     */
    public function getItems(Request $request): Collection
    {
        return $this->getModelClassName()::all()->map->only($this->listItemAttributes);
    }

    public function getItem(Request $request, $id): array
    {
        return $this->getModelClassName()::find($id)->only($this->singleItemAttributes);
    }

    public function getView(string $action)
    {
        $root = str_replace('/', '.', str_replace('views/', '', config('persources.view_root')));

        return implode('.', [$root, strtolower($this->pluralName), $action]);
    }

    public function getHttpMethod(string $action)
    {
        return Facades\Persources::getHttpMethod($action);
    }

    /**
     * From all $actions supported by this Resource, this method returns only the ones that the user may perform
     * according to his permissions.
     */
    public function getActionsForCurrentUser(): array
    {
        $userActions = [];
        foreach ($this->actions as $action) {
            foreach ($this->permissions as $permission) {
                if (! in_array($action, $userActions) &&
                    in_array($action, Facades\Persources::getImpliedActions(Facades\Persources::getAction($permission))) &&
                    Facades\Persources::checkPermission($permission)) {
                    $userActions[] = $action;
                }
            }
        }

        return $userActions;
    }

    /**
     * Returns the class name for the model that can be used for static calls
     */
    public function getModelClassName(): string
    {
        return '\\'.$this->model;
    }
}
