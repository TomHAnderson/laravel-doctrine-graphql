<?php

namespace ApiSkeletons\Laravel\Doctrine\GraphQL;

use Illuminate\Support\ServiceProvider;

class ServiceProvider extends ServiceProvider 
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/graphql.php' => config_path('graphql.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                Command\ConfigurationSkeletonCommand::class,
            ]);
        }
    }
}
