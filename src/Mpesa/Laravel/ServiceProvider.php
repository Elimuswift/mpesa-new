<?php

namespace Elimuswift\Mpesa\Laravel;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider as RootProvider;
use Elimuswift\Mpesa\C2B\Identity;
use Elimuswift\Mpesa\C2B\Registrar;
use Elimuswift\Mpesa\C2B\STK;
use Elimuswift\Mpesa\Contracts\CacheStore;
use Elimuswift\Mpesa\Contracts\ConfigurationStore;
use Elimuswift\Mpesa\Engine\Core;
use Elimuswift\Mpesa\Laravel\Stores\LaravelCache;
use Elimuswift\Mpesa\Laravel\Stores\LaravelConfig;

class ServiceProvider extends RootProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../../assets/config/mpesa.php' => config_path('mpesa.php'),
        ]);
        $this->publishes([
            __DIR__.'/../../../assets/keys/cert.cer' => storage_path('mpesa_public.key'),
        ]);
    }

    /**
     * Registrar the application services.
     */
    public function register()
    {
        $this->bindInstances();
        $this->registerFacades();
    }

    private function bindInstances()
    {
        $this->app->bind(ConfigurationStore::class, LaravelConfig::class);
        $this->app->bind(CacheStore::class, LaravelCache::class);
        $this->app->bind(Core::class, function ($app) {
            $config = $app->make(ConfigurationStore::class);
            $cache = $app->make(CacheStore::class);

            return new Core(new Client(), $config, $cache);
        });
    }

    private function registerFacades()
    {
        $this->app->bind('mp_stk', function () {
            return $this->app->make(STK::class);
        });

        $this->app->bind('mp_registrar', function () {
            return $this->app->make(Registrar::class);
        });

        $this->app->bind('mp_identity', function () {
            return $this->app->make(Identity::class);
        });

        //        $this->app->bind('mpesa', function () {
//            return $this->app->make(Cashier::class);
//        });
    }
}
