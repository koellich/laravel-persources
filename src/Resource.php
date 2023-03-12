<?php

namespace Koellich\Persources;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class Resource
{
    /**
     * @var string Full qualified name of the Model that this resource represents.
     */
    protected string $model;

    /**
     * @var string Singular Name of the resource.
     */
    protected string $singularName;

    /**
     * @var string Plural name of the resource.
     */
    protected string $pluralName;

    /**
     * @var array Array of permissions (string) that are handled by this resource.
     */
    protected array $permissions = [];

    /**
     * @var array Array of model attributes that can be used when displaying a list of models.
     */
    protected array $listItemAttributes = [];

    /**
     * @var array Array of model attributes that can be used when displaying a single model.
     */
    protected array $singleItemAttributes = [];

    public function list(Request $request)
    {
        return view($this->getView('list'), ['items' => $this->getItems($request)]);
    }

    public function view(Request $request, $id)
    {
        return view($this->getView('view'), ['item' => $this->getItem($request, $id)]);
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
    protected function getItems(Request $request): Collection
    {
        return $this->getModelClassName()::all()->only($this->listItemAttributes);
    }

    protected function getItem(Request $request, $id): Model
    {
        return $this->getModelClassName()::find($id)->only($this->singleItemAttributes);
    }

    /**
     * Returns the translation of the $name
     */
    protected function getName()
    {
        return __($this->name);
    }

    protected function getView(string $action)
    {
        return implode('.', [config('view_root'), $this->pluralName, $action]);
    }

    /**
     * Returns the class name for the model that can be used for static calls
     */
    private function getModelClassName(): string
    {
        return '\\'.$this->model;
    }
}
