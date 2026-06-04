<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('make:crud {model}')]
#[Description('Génère un CRUD')]
class MakeCrud extends Command
{
    /** @var array<string, string> */
    protected const array STUBS = [
        'controller' => __DIR__ . '/stubs/controller.stub',
        'route'      => __DIR__ . '/stubs/web.stub',
        'model'      => __DIR__ . '/stubs/model.stub',
    ];

    public function handle(): int
    {
        $model = Str::studly($this->argument('model'));

        $this->call('make:model', ['name' => $model]);
        $this->call('make:controller', ['name' => $model.'Controller']);
        $this->call('make:migration', [
            'name' => 'create_'.Str::snake(Str::pluralStudly($model)).'_table',
        ]);

        $this->createViews($model);
        $this->updateController($model);
        $this->updateRoute($model);
        $this->updateModel($model);

        $this->info("CRUD généré pour {$model}.");

        return self::SUCCESS;
    }

    protected function getStub(string $stub): string
    {
        return file_get_contents(self::STUBS[$stub]);
    }

    protected function viewNamespace(string $model): string
    {
        return Str::snake(Str::pluralStudly($model));
    }

    protected function routeName(string $model): string
    {
        return Str::snake($model);
    }

    protected function createViews(string $model): void
    {
        $path = resource_path('views/'.$this->viewNamespace($model).'/');

        if (! file_exists($path)) {
            mkdir($path.'partials', 0755, true);
        }

        foreach (['index', 'create', 'edit', 'show'] as $view) {
            file_put_contents($path.$view.'.blade.php', '');
        }

        file_put_contents($path.'partials/form.blade.php', '');
    }

    protected function updateController(string $model): void
    {
        $path = app_path("Http/Controllers/{$model}Controller.php");

        $content = str_replace(
            ['{{ $model }}', '{{ $controllerName }}', '{{ $view }}', '{{ $name }}'],
            [$model, $model.'Controller', $this->viewNamespace($model), $this->routeName($model)],
            $this->getStub('controller'),
        );

        file_put_contents($path, $content);
    }

    protected function updateRoute(string $model): void
    {
        $content = str_replace(
            ['{{ $controllerName }}', '{{ $name }}'],
            [$model.'Controller', $this->routeName($model)],
            $this->getStub('route'),
        );

        file_put_contents(base_path('routes/web.php'), PHP_EOL.$content, FILE_APPEND);
    }

    protected function updateModel(string $model): void
    {
        $path = app_path("Models/{$model}.php");

        $content = str_replace(
            ['{{ $model }}'],
            [$model],
            $this->getStub('model'),
        );

        file_put_contents($path, $content);
    }
}
