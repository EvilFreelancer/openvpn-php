<?php

namespace OpenVPN\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../configs/openvpn-client.php' => config_path('openvpn-client.php'),
            __DIR__ . '/../../configs/openvpn-server.php' => config_path('openvpn-server.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../configs/openvpn-client.php', 'openvpn-client'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../../configs/openvpn-server.php', 'openvpn-server'
        );

        $this->app->bind(ConfigWrapper::class);
    }
}
