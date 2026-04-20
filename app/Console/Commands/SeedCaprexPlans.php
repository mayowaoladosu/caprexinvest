<?php

namespace App\Console\Commands;

use App\Models\InvestmentPlan;
use Illuminate\Console\Command;

class SeedCaprexPlans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:seed-caprex-plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed production-ready Gold and BTC investment plans';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $plans = [
            [
                'plan_name' => 'Gold Starter',
                'asset_type' => 'gold',
                'min_invest' => 300,
                'max_invest' => 3000,
                'plan_period' => 6,
                'plan_period_type' => 'Month',
                'return_period' => 7,
                'return_period_type' => 'Day',
                'number_of_profit_return' => 24,
                'profit' => 1.9,
                'profit_type' => 'Percentage',
                'maturity' => 180,
                'short_description' => 'Balanced plan with weekly returns and moderate entry amount.',
            ],
            [
                'plan_name' => 'Gold Premium',
                'asset_type' => 'gold',
                'min_invest' => 5000,
                'max_invest' => 50000,
                'plan_period' => 12,
                'plan_period_type' => 'Month',
                'return_period' => 7,
                'return_period_type' => 'Day',
                'number_of_profit_return' => 48,
                'profit' => 2.4,
                'profit_type' => 'Percentage',
                'maturity' => 365,
                'short_description' => 'Higher-capacity gold strategy for long-term wealth growth.',
            ],
            [
                'plan_name' => 'BTC Growth',
                'asset_type' => 'btc',
                'min_invest' => 200,
                'max_invest' => 2500,
                'plan_period' => 4,
                'plan_period_type' => 'Month',
                'return_period' => 7,
                'return_period_type' => 'Day',
                'number_of_profit_return' => 16,
                'profit' => 2.8,
                'profit_type' => 'Percentage',
                'maturity' => 120,
                'short_description' => 'BTC-focused plan for users targeting medium-term growth.',
            ],
            [
                'plan_name' => 'BTC Aggressive',
                'asset_type' => 'btc',
                'min_invest' => 3000,
                'max_invest' => 30000,
                'plan_period' => 9,
                'plan_period_type' => 'Month',
                'return_period' => 7,
                'return_period_type' => 'Day',
                'number_of_profit_return' => 36,
                'profit' => 3.2,
                'profit_type' => 'Percentage',
                'maturity' => 270,
                'short_description' => 'Advanced BTC portfolio strategy with stronger risk/reward profile.',
            ],
        ];

        foreach ($plans as $plan) {
            InvestmentPlan::updateOrCreate(
                [
                    'plan_name' => $plan['plan_name'],
                    'asset_type' => $plan['asset_type'],
                ],
                [
                    'short_description' => $plan['short_description'],
                    'plan_price' => null,
                    'plan_period' => $plan['plan_period'],
                    'plan_period_type' => $plan['plan_period_type'],
                    'min_invest' => $plan['min_invest'],
                    'max_invest' => $plan['max_invest'],
                    'return_typ_has_lifetime' => 0,
                    'amount_has_fixed' => 0,
                    'return_period' => $plan['return_period'],
                    'return_period_type' => $plan['return_period_type'],
                    'unlimited_period' => 0,
                    'number_of_profit_return' => $plan['number_of_profit_return'],
                    'profit' => $plan['profit'],
                    'profit_type' => $plan['profit_type'],
                    'capital_back' => 1,
                    'maturity' => $plan['maturity'],
                    'status' => 1,
                    'soft_delete' => 0,
                ]
            );
        }

        $this->info('Caprex Gold/BTC plans seeded successfully.');
        return self::SUCCESS;
    }
}

