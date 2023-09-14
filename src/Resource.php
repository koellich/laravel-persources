<?php

namespace Koellich\Persources;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Response;

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
     * @var array Array of actions that are available.
     */
    public array $actions = [];

    /**
     * Column Definitions
     *
     * Available keys are:
     *
     * name: string - name of DB column
     * dbType: string - type of the column in the DB; null for transient columns
     * label: string - human readable name
     * showInList: boolean - show this column in a list view?
     * showOnSingleItem: boolean - show this column in a single item view?
     * readonly: boolean - should this column be presented read-only?
     * htmlType: select, radiogroup, checkboxgroup or https://www.w3schools.com/html/html_form_input_types.asp
     * choices: array(value => label) - map of choices for type select, radiogroup of checkboxgroup
     * placeholder: string - placeholder for html input element
     * required: boolean - make html input element required?
     * class: string - class attribute on html input element
     * pattern: string - pattern attribute on the html input element
     *
     * @returns array Array of Column Definitions
     */
    public function columnDefinitions(): array
    {
        return [];
    }

    /**
     * Datasets are different sets of data (i.e. diefferent queries) that can be used to get items.
     *
     * Available keys are:
     *
     * name: string
     * query: fn ($query) => $query->doSomethingWithIt(...)
     *
     * @returns array Array of Datasets
     */
    public function datasets(): array
    {
        return [
            ['name' => __('resources.dataset_default'), 'query' => fn ($query) => $query],
        ];
    }

    /**
     * Append the $query such that a search is performed using the given $search string.
     * The default implementation does nothing. Subclasses can override this to implement search.
     *
     * @param  ?string  $search
     * @return mixed Return the modified $query
     */
    public function addSearchClause($query, ?string $search)
    {
        return $query;
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
     * Override this to customize. Or use datasets() to allow for different queries.
     *
     * Defaults to all items, i.e.: $this->getModelClassName()::query()
     *
     * @return mixed Query
     */
    public function query(): mixed
    {
        return $this->getModelClassName()::query();
    }

    /**
     * Returns the query for the given $dataset.
     * Override this to customize.
     * Defaults to all items, i.e.: $this->getModelClassName()::query()
     *
     * @param  string|null  $dataset optional dataset name. if omitted, the query() is used.
     * @return mixed Query
     */
    public function datasetQuery(string $dataset = null)
    {
        if (! $dataset) {
            return $this->query();
        }
        $ds = array_values(array_filter($this->datasets(), fn ($ds) => $ds['name'] == $dataset));

        return count($ds) == 0 ? $this->query() : $ds[0]['query']($this->query());
    }

    /**
     * Creates a new model
     *
     * @return \Illuminate\Http\Response|never
     */
    public function create(array $values)
    {
        $ok = $this->getModelClassName()::create($values);

        return $ok ? Response::noContent() : abort(400);
    }

    /**
     * Updates the model with the given $id if it is in the query()'s result set.
     *
     * @return \Illuminate\Http\Response|never
     */
    public function update($id, array $values)
    {
        $ok = $this->query()->find($id)->update($values);

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
     * @param  string  $dataset The dataset name (optional). If omitted, the query() will be used.
     */
    public function getItemCount(string $search = null, string $dataset = null): int
    {
        $query = $this->datasetQuery($dataset);
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
     * @param  string  $dataset The dataset name (optional). If omitted, the query() will be used.
     */
    public function getItems(int $offset = 0, int $count = null, string $search = null,
        string $orderBy = null, string $orderDirection = 'ASC', string $dataset = null)
    {
        $query = $this->datasetQuery($dataset);
        if ($count) {
            $query = $query->take($count)->skip($offset);
        }
        if ($orderBy) {
            $query = $query->orderBy($orderBy, $orderDirection);
        }
        if ($search) {
            $query = $this->addSearchClause($query, $search);
        }

        $columns = array_map(fn ($c) => $c['name'], $this->columnDefinitionsForList());

        return $query->get()->map->only($columns);
    }

    /**
     * Using the query(), getItem($id) returns the model with the given $id as a dict containing only $singleItemAttributes
     */
    public function getItem($id): array
    {
        $columns = array_map(fn ($c) => $c['name'], $this->columnDefinitionsForSingleItem());

        return $this->query()->find($id)->only($columns);
    }

    /**
     * @return array columnDefinitions() where "showInList" is true^
     */
    public function columnDefinitionsForList(): array
    {
        return array_filter($this->columnDefinitions(), (fn ($c) => $c['showInList'] == true));
    }

    /**
     * @return array columnDefinitions() where "showOnSingleItem" is true
     */
    public function columnDefinitionsForSingleItem(): array
    {
        return array_filter($this->columnDefinitions(), (fn ($c) => $c['showOnSingleItem'] == true));
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

    /**
     * @return string The translated model name as defined by the key resources.<singularName>
     */
    public function translatedModelName(): string
    {
        return __('resources.'.strtolower($this->singularName));
    }

    /**
     * @return string The translated model name as defined by the key resources.<singularName>
     */
    public function translatedModelNamePlural(): string
    {
        return __('resources.'.strtolower($this->pluralName));
    }
}
