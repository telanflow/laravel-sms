<?php

namespace Telanflow\Sms;

use Telanflow\Sms\Storage\CacheStorage;
use Illuminate\Support\Facades\Route;
use Overtrue\EasySms\EasySms;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
	/**
	 * @var string
	 */
	protected $namespace = 'Telanflow\Sms';

	/**
	 * Boot the service provider.
	 */
	public function boot()
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/../config/config.php' => config_path('sms.php'),
			]);

			$this->loadMigrationsFrom(__DIR__ . '/../migrations');
		}

		if (!$this->app->routesAreCached())
        {
			$routeAttr = config('sms.route', []);
			if (config('sms.enable_rate_limit')) {
				$routeAttr['middleware'] = array_merge($routeAttr['middleware'], [
                    config('sms.rate_limit_middleware') . ':' . config('sms.rate_limit_count') . ',' . config('sms.rate_limit_time')
                ]);
			}

			Route::group(array_merge(['namespace' => $this->namespace], $routeAttr), function ($router) {
				require_once __DIR__ . '/route.php';
			});
		}
	}

	/**
	 * Register the service provider.
	 */
	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'sms');

		$this->app->singleton(Sms::class, function ()
        {
			$storage = config('sms.storage', CacheStorage::class);

			return new Sms(new EasySms(config('sms.easy_sms')), new $storage());
		});
	}

	/**
	 * @return array
	 */
	public function provides()
	{
		return [Sms::class];
	}
}
