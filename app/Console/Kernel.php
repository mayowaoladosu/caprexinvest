<?php

namespace App\Console;

use App\Console\Commands\GatewayCurrencyUpdate;
use App\Console\Commands\GoldRateUpdate;
use App\Console\Commands\PayoutCryptoCurrencyUpdateCron;
use App\Console\Commands\PayoutCurrencyUpdateCron;
use App\Console\Commands\PlanInvestmentStatus;
use App\Models\Deposit;
use App\Models\Payout;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{


    protected $commands = [
        PayoutCurrencyUpdateCron::class,
        GatewayCurrencyUpdate::class,
        PayoutCryptoCurrencyUpdateCron::class,
        PlanInvestmentStatus::class,
        GoldRateUpdate::class
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        $schedule->command('plan-investment-status')->everySecond();

        $schedule->command('app:gold-rate-update')->daily();

        $schedule->command('model:prune', [
            '--model' => [Deposit::class, Payout::class],
        ])->daily();


        $basicControl = basicControl();
        if ($basicControl->currency_layer_auto_update == 1) {
            $schedule->command('gateway-currency-update')->{basicControl()->currency_layer_auto_update_at}();
            $schedule->command('payout-currency:update')
                ->{basicControl()->currency_layer_auto_update_at}();
        }
        if ($basicControl->coin_market_cap_auto_update == 1) {
            $schedule->command('payout-crypto-currency-update-cron')->{basicControl()->coin_market_cap_auto_update_at}();
        }

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
