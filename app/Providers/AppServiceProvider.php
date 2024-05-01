<?php

namespace App\Providers;

use App\Repositories\CurrencyRepository;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            CurrencyRepository::class,
            fn () => new CurrencyRepository(Redis::connection('currency_list'))
        );
    }

    public function boot(): void
    {
        //
    }
}
