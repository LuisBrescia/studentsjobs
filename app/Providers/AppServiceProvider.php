<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Predis\Command\RedisFactory;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\NativeClock;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, Client::class);

        $this->app->bind(ClockInterface::class, function () {
            return new NativeClock();
        });

        $this->app->bind('Illuminate\Contracts\Redis\Factory', function ($app) {
            return $app['redis'];
        });

        $this->app->bind(RedisFactory::class, function ($app) {
            return $app['redis'];
        });

        $this->app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \App\Exceptions\Handler::class
        );

        $this->app->singleton('redis', function ($app) {
            return new \Illuminate\Redis\RedisManager($app, 'predis', [
                'default' => [
                    'url' => env('REDIS_URL'),
                    'host' => env('REDIS_HOST', '127.0.0.1'),
                    'password' => env('REDIS_PASSWORD'),
                    'port' => env('REDIS_PORT', 6379),
                    'database' => '0',
                ],
            ]);
        });

        $this->app->when(\Kreait\Firebase\Auth\ApiClient::class)
            ->needs('$projectId')
            ->give(env('FIREBASE_PROJECT_ID'));

        $this->app->when(\Kreait\Firebase\Auth\SignIn\GuzzleHandler::class)
            ->needs('$projectId')
            ->give(env('FIREBASE_PROJECT_ID'));
    }

    public function boot(): void
    {
        //
    }
}
