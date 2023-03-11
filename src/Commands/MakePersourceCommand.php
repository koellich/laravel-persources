<?php

namespace Koellich\Persources\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakePersourceCommand extends Command
{
    public $signature = 'make:persource 
                         {model : The model for which to create a resource and permissions. <fg=green>Car</> or <fg=green>App\\Models\\Car</>} 
                         {actions* : Actions for which to create permissions. Use a combination of the following: <fg=green>list</>, <fg=green>view</>, <fg=green>create</>, <fg=green>update</>, <fg=green>delete</>, or use <fg=green>none</> to avoid creating permissions }';

    public $description = 'Create a new Persource';

    public function handle(): int
    {
        $model = $this->argument("model");
        // assume default namespace if it is not specified
        if (!str_contains("\\", $model)) {
            $model = "App\\Models\\$model";
        }
        $actions = $this->argument("actions");

        $this->info("Generate Resource for Model $model");
        $this->info(count($actions)==0 ?
            "Do not generate any permissions. Be sure to add them to the resource's \$permissions attribute" :
            "Generate Permissions for actions: " . implode(", ", $actions));

        $singularName = $this->last( "\\", $model);
        $pluralName = Str::plural($singularName);

        $this->info('All done');

        return self::SUCCESS;
    }

    /**
     * @param string $separator
     * @param string $string
     * @return string The last part of a $string separatated by $separator
     */
    private function last(string $separator, string $string)
    {
        $parts = explode($separator, $string);
        return $parts[array_key_last($parts)];
    }
}
