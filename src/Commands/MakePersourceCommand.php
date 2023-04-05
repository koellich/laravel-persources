<?php

namespace Koellich\Persources\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Koellich\Persources\Facades\Persources;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;

class MakePersourceCommand extends Command
{
    public $signature = 'make:persource 
                         {model : The model for which to create a resource and permissions. <fg=green>Car</> or <fg=green>App\\Models\\Car</>} 
                         {actions* : Actions for which to create permissions. Use a combination of: <fg=green>list</>, <fg=green>view</>, <fg=green>read</>, <fg=green>create</>, <fg=green>update</>, <fg=green>update</>, <fg=green>delete</>, or use <fg=green>none</> to avoid creating permissions}
                         {--P|permission=* : Existing permissions to use for this Resource}
                         {--prefix=} The prefix to use when generating permissions. --prefix=my will create my.cars.read etc.
                         {--noMigration} If specified, permissions will be created directly in the DB instead of creating a migration';

    public $description = 'Create a new Persource';

    public function handle(): int
    {
        $model = $this->argument('model');
        // assume default namespace if it is not specified
        if (! str_contains('\\', $model)) {
            $model = "App\\Models\\$model";
        }
        $actions = $this->argument('actions');
        if (in_array('none', $actions)) {
            $actions = [];
        }
        $existingPermissions = $this->option('permission');
        $prefix = $this->option('prefix');
        $noMigration = $this->option('noMigration');

        $this->info("Generate Resource for Model $model");

        $additionalInfo = count($existingPermissions) == 0 ?
            "Be sure to add them to the resource's \$permissions attribute" :
            'Using exsiting permissions: '.implode(', ', $existingPermissions);
        $this->info(count($actions) == 0 ?
            "Do not generate any permissions. $additionalInfo" :
            'Generate Permissions for actions: '.implode(', ', $actions));

        $singularName = Str::of($model)->afterLast('\\')->toString();
        $pluralName = Str::plural($singularName);

        $permissions = $this->createPermissions($pluralName, $prefix, $actions, ! $noMigration);

        $allPermissions = array_unique(array_merge($permissions, $existingPermissions));

        $this->generateResource($model, $singularName, $pluralName, $allPermissions, $actions);

        $this->copyViews($pluralName, $allPermissions);

        $this->info('All done');

        return self::SUCCESS;
    }

    private function createPermissions($pluralName, $prefix, array $actions, $migration): array
    {
        $permissions = [];
        $permissionModel = config('permission.models.permission');
        if ($migration) {
            $migrationContent = file_get_contents($this->getStubPath('add_permissions_migration.phpstub'));
            $migrationContent = str_replace('%USE%', "use $permissionModel;\n", $migrationContent);
            $migrationUp = '';
            $migrationDown = '';
        }
        foreach ($actions as $action) {
            if ($action !== 'none') {
                if (! in_array($action, ['list', 'view', 'read', 'create', 'update', 'write', 'delete'])) {
                    $this->warn("Unknown action $action");
                } else {
                    $permission = $prefix ? strtolower("$prefix.$pluralName.$action") : strtolower("$pluralName.$action");
                    $permissions[] = $permission;
                    try {
                        if ($migration) {
                            $migrationUp .= "        $permissionModel::create(['name' => '$permission']);\n";
                            $migrationDown .= "        $permissionModel::where('name', '=', '$permission')->delete();\n";
                        } else {
                            $permissionModel::create(['name' => $permission]);
                            $this->info("Created $permissionModel for $permission");
                        }
                    } catch (PermissionAlreadyExists) {
                        $this->warn("$permissionModel for $permission already exists and will not be overwritten.");
                    }
                }
            }
        }
        if ($migration) {
            $migrationContent = str_replace('%UP%', $migrationUp, $migrationContent);
            $migrationContent = str_replace('%DOWN%', $migrationDown, $migrationContent);
            $migrationFilename = Carbon::now()->format('Y_m_d_u').'_add_'.strtolower($pluralName).'_permissions.php';
            file_put_contents(database_path("migrations/$migrationFilename"), $migrationContent);
            $this->info("Created migration $migrationFilename for $pluralName permissions");
        }

        return $permissions;
    }

    /**
     * Create a Resource in the resources directory by copying the stub and replacing placeholders with real values.
     */
    private function generateResource(string $model, string $singularName, string $pluralName, array $permissions, array $actions): void
    {
        $resourcesNamespace = config('persources.resources_namespace');
        $resourceClassName = ucfirst($pluralName).'Resource';
        $resourceFQN = $resourcesNamespace.'\\'.$resourceClassName;
        $resourcesDir = Persources::getResourcesPath();
        $resourceFilename = "$resourcesDir/$resourceClassName.php";

        $this->ensureDir($resourcesDir);

        if (! file_exists($resourceFilename)) {
            $res = file_get_contents($this->getStubPath('Resource.phpstub'));

            $availableActions = array_unique(Arr::flatten(array_map(fn ($action) => Persources::getImpliedActions($action), $actions)));
            $availableActions = array_filter($availableActions, fn ($action) => ! in_array($action, ['read', 'write', 'list']));

            foreach ([
                '%NAMESPACE%' => $resourcesNamespace,
                '%RESOURCE%' => $resourceClassName,
                '%MODEL%' => "'$model'",
                '%SINGULAR_NAME%' => "'$singularName'",
                '%PLURAL_NAME%' => "'$pluralName'",
                '%PERMISSIONS%' => $this->formatArray($permissions),
                '%LISTITEM_ATTRIBUTES%' => "['id']",
                '%SINGLEITEM_ATTRIBUTES%' => "['id']",
                '%ACTIONS%' => $this->formatArray($availableActions)] as $placeholder => $value) {
                $res = str_replace($placeholder, $value, $res);
            }
            file_put_contents($resourceFilename, $res);
            $this->info("Created $resourceFQN");
        } else {
            $this->warn("$resourceFQN already exists and will not be overwritten.");
        }
    }

    private function copyViews(string $pluralName, array $permissions)
    {
        $targetDir = Persources::getViewsPath().'/'.strtolower($pluralName);
        $this->ensureDir($targetDir);
        $alreadyCreatedViews = [];
        foreach ($permissions as $permission) {
            $action = Persources::getAction($permission);
            $impliedActions = Persources::getImpliedActions($action);
            foreach ($impliedActions as $impliedAction) {
                $filename = "$impliedAction.blade.php";
                if (! in_array($filename, $alreadyCreatedViews)) {
                    $source = $this->getViewStubPath($filename);
                    if (file_exists($source)) {
                        $target = "$targetDir/$filename";
                        if (! file_exists($target)) {
                            copy($source, $target);
                            $this->info('Created view '.str_replace(base_path(), '', $target));
                            $alreadyCreatedViews[] = $filename;
                        } else {
                            $this->warn("View $filename already exists and will not be overwritten");
                        }
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    private function getViewStubPath($filename)
    {
        $path = config('persources.view_stubs_path');
        return $path ? Str::finish($path, '/') . $filename : $this->getStubPath($filename);
    }

    /**
     * @return string
     */
    private function getStubPath($filename)
    {
        return __DIR__."/../../stubs/$filename";
    }

    private function ensureDir(string $dir): void
    {
        if (! file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    private function formatArray(array $arr): string
    {
        return '['.(count($arr) == 0 ? '' : "'".implode("', '", $arr)."'").']';
    }
}
