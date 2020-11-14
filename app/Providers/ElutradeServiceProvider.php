<?php

namespace App\Providers;

use App\Elutrade\Payment\Payment as ElutradePayment;
use App\Elutrade\Transaction\Transaction as ElutradeTransaction;
use App\Models\TransactionPayment;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ElutradeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ElutradeTransaction::class, function ($app) {
            return new ElutradeTransaction($app);
        });

        $this->app->bind(ElutradePayment::class, function ($app) {
            return new ElutradePayment($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function provides()
    {
        return [ElutradeTransaction::class, ElutradePayment::class];
    }
}
