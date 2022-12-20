<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Scout\Console\ImportCommand;

class ImportElasticsearchProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            ImportCommand::class,
        ]);
    }
}
