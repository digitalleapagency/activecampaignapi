<?php

namespace DigitalLeapAgency\ActiveCampaign;

use DigitalLeapAgency\ActiveCampaign\Console\InstallActiveCampaign;
use Illuminate\Support\ServiceProvider;

class ActiveCampaignServiceProvider extends ServiceProvider {
    /**
     * Register services.
     *
     * @return void
     */
    public function register() {

        $this->app->bind('contact', function ($app) {
            return new Contact();
        });

        $this->app->bind('tag', function ($app) {
            return new Tag();
        });

        $this->app->bind('event', function ($app) {
            return new Event();
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/config/config.php' => config_path('activecampaign.php'),
            ], 'config');

            $this->commands([
                InstallActiveCampaign::class,
            ]);
        }

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }
}