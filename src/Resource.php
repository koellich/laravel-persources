<?php

namespace Koellich\Persources;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

class Resource
{
    /**
     * @var string Unique name of this resource. Can be used in Persources::getResourceByName($name)
     */
    public string $name;

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
     * @var array Array of model attributes that can be used when displaying a list of models. Defaults to all columns that are not $hidden.
     */
    public array $listItemAttributes;

    /**
     * @var array Array of model attributes that can be used when displaying a single model. Defaults to all columns that are not $hidden.
     */
    public array $singleItemAttributes;

    /**
     * @var array Array of actions that are available.
     */
    public array $actions = [];

    public function __construct()
    {
        $this->listItemAttributes = $this->getPublicModelColumns();
        $this->singleItemAttributes = $this->listItemAttributes;
    }

    /**
     * Returns all columns of the Resource's $model that are not $hidden
     * @return array
     */
    private function getPublicModelColumns(): array
    {
        $model = new ($this->getModelClassName())();
        $columns = Schema::getColumnListing($model->getTable());
        return array_diff($columns, $model->getHidden());
    }

    public function list()
    {
        return view($this->getView('list'), ['resource' => $this]);
    }

    public function view($id)
    {
        return view($this->getView('view'), ['resource' => $this, 'item' => $this->getItem($id)]);
    }

    /**
     * Returns the query to be used for fetching the models, either for list or single items.
     * Override this to customize.
     * Defaults to all items, i.e.: $this->getModelClassName()::query()
     *
     * @return void
     */
    public function query()
    {
        return $this->getModelClassName()::query();
    }

    /**
     * Append the $query such that a search is performed using the given $search string.
     * The default implementation does nothing. Subclasses can override this to implement search.
     *
     * @return mixed Return the modified $query
     */
    public function addSearchClause($query, string $search)
    {
        return $query;
    }

    /**
     * Creates a new model
     *
     * @return \Illuminate\Http\Response|never
     */
    public function create(Request $request)
    {
        $ok = $this->getModelClassName()::create($request->all());

        return $ok ? Response::noContent() : abort(400);
    }

    /**
     * Updates the model with the given $id if it is in the query()'s result set.
     *
     * @return \Illuminate\Http\Response|never
     */
    public function update(Request $request, $id)
    {
        $ok = $this->query()->find($id)->update($request->all());

        return $ok ? Response::noContent() : abort(400);
    }

    /**
     * Deletes the model with the given $id if it is in the query()'s result set.
     *
     * @return \Illuminate\Http\Response|never
     */
    public function delete($id)
    {
        $ok = $this->query()->find($id)->delete();

        return $ok ? Response::noContent() : abort(400);
    }

    /**
     * Returns the item count using the query()
     *
     * @param  string  $search Search term (optional)
     */
    public function getItemCount(?string $search = null): int
    {
        $query = $this->query();
        if ($search) {
            $query = $this->addSearchClause($query, $search);
        }

        return $query->count();
    }

    /**
     * Returns a list of models using the query().
     * Result is a collection of dicts containing only $listItemAttributes
     *
     * @param  int  $offset The first result of the query
     * @param  ?int  $count The number of items to return. If null, then all items are returned.
     * @param  string  $search search term (optional). If present, the addSearchClause($query) function will be called.
     * @param  string  $orderBy column to order by. Or null to omit order by clause
     * @param  string  $orderDirection ASC or DESC. Default: ASC
     */
    public function getItems(int $offset = 0, ?int $count = null, ?string $search = null, ?string $orderBy = null, string $orderDirection = 'ASC')
    {
        $query = $this->query();
        if ($count) {
            $query = $query->take($count)->skip($offset);
        }
        if ($orderBy) {
            $query = $query->orderBy($orderBy, $orderDirection);
        }
        if ($search) {
            $query = $this->addSearchClause($query, $search);
        }

        return $query->get()->map->only($this->listItemAttributes);
    }

    /**
     * Using the query(), getItem($id) returns the model with the given $id as a dict containing only $singleItemAttributes
     */
    public function getItem($id): array
    {
        return $this->query()->find($id)->only($this->singleItemAttributes);
    }

    public function getView(string $action)
    {
        $root = str_replace('/', '.', str_replace('views/', '', config('persources.view_root')));

        return implode('.', [$root, strtolower($this->pluralName), $action]);
    }

    /**
     * From all $actions supported by this Resource, this method returns only the ones that the user may perform
     * according to his permissions.
     *
     * @return array [['name' => 'edit', 'method' => 'PATCH'], ...]
     */
    public function getActionsForCurrentUser(): array
    {
        $userActions = [];
        foreach ($this->actions as $action) {
            $impliedActions = Facades\Persources::getImpliedActions($action);
            foreach ($impliedActions as $impliedAction) {
                foreach ($this->permissions as $permission) {
                    $alreadyAdded = Arr::first($userActions, fn ($userAction) => $userAction['name'] == $impliedAction) != null;
                    if (! $alreadyAdded) {
                        $permissionImpliesAction = in_array($impliedAction,
                            Facades\Persources::getImpliedActions(Facades\Persources::getAction($permission)));
                        $currentUserHasPermission = Facades\Persources::checkPermission($permission);
                        if ($permissionImpliesAction && $currentUserHasPermission) {
                            $userActions[] = ['name' => $impliedAction, 'method' => Facades\Persources::getHttpMethod($impliedAction)];
                        }
                    }
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

    /**
     * Returns all permissions that are handled by this resource.
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }
}
