<?php

namespace App\Console\Commands;

use App\Models\PayoutMethod;
use App\Models\WithdrawalDay;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConfigureBtcManualPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production:configure-btc-manual-payments {--wallet=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable BTC manual-only deposit and withdrawal methods, disable all other payment methods';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $walletAddress = trim((string) ($this->option('wallet') ?: 'bc1q3fxmnuuj2ymaq6lgw3q70vvs3yqzcsx2dgr6pa'));
        if ($walletAddress === '') {
            $this->error('BTC wallet address cannot be empty.');
            return self::FAILURE;
        }

        $logoPath = 'branding/btc-logo.svg';
        $logoDriver = 'local';

        DB::transaction(function () use ($walletAddress, $logoPath, $logoDriver) {
            // Disable all deposit gateways first.
            DB::table('gateways')->update(['status' => 0]);

            // Ensure BTC manual deposit gateway is always treated as manual (id > 999).
            DB::table('gateways')->updateOrInsert(
                ['id' => 1001],
                [
                    'code' => 'btc-manual-deposit',
                    'name' => 'Bitcoin Manual Deposit',
                    'sort_by' => 1,
                    'image' => $logoPath,
                    'driver' => $logoDriver,
                    'status' => 1,
                    'parameters' => json_encode([
                        'payment_receipt' => [
                            'field_name' => 'Payment Receipt',
                            'field_label' => 'Payment Receipt (Screenshot)',
                            'type' => 'file',
                            'validation' => 'required',
                        ],
                        'sender_wallet' => [
                            'field_name' => 'Sender Wallet',
                            'field_label' => 'Sender Wallet Address (Optional)',
                            'type' => 'text',
                            'validation' => 'optional',
                        ],
                        'txid' => [
                            'field_name' => 'Transaction Hash',
                            'field_label' => 'Transaction Hash (Optional)',
                            'type' => 'text',
                            'validation' => 'optional',
                        ],
                    ]),
                    'currencies' => null,
                    'extra_parameters' => null,
                    'supported_currency' => json_encode(['BTC']),
                    'receivable_currencies' => json_encode([
                        [
                            'currency' => 'BTC',
                            'conversion_rate' => 1,
                            'min_limit' => 0.0001,
                            'max_limit' => 10,
                            'percentage_charge' => 0,
                            'fixed_charge' => 0,
                        ],
                    ]),
                    'description' => 'Manual Bitcoin deposit. Upload receipt after transfer.',
                    'currency_type' => 0,
                    'is_sandbox' => 0,
                    'environment' => 'live',
                    'is_manual' => 1,
                    'note' => 'Send BTC to this wallet address: <strong>' . e($walletAddress) . '</strong><br>Upload payment receipt/screenshot after transfer. Deposits are verified manually.',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            // Disable all payout methods first, then keep one manual BTC method active.
            DB::table('payout_methods')->update(['is_active' => 0]);

            $inputForm = [
                'receiver_wallet' => [
                    'field_name' => 'receiver_wallet',
                    'field_label' => 'Your BTC Wallet Address',
                    'type' => 'text',
                    'validation' => 'required',
                ],
            ];

            $payoutCurrencies = [
                [
                    'currency_symbol' => 'BTC',
                    'conversion_rate' => 1,
                    'min_limit' => 0.0001,
                    'max_limit' => 10,
                    'percentage_charge' => 0,
                    'fixed_charge' => 0,
                ],
            ];

            $payoutMethod = PayoutMethod::query()->firstOrNew(['code' => 'btc-manual-withdraw']);
            $payoutMethod->name = 'Bitcoin Manual Withdraw';
            $payoutMethod->description = 'Manual BTC withdraw request. Enter amount and your receiving wallet.';
            $payoutMethod->banks = null;
            $payoutMethod->parameters = null;
            $payoutMethod->extra_parameters = null;
            $payoutMethod->inputForm = $inputForm;
            $payoutMethod->currency_lists = null;
            $payoutMethod->supported_currency = ['BTC'];
            $payoutMethod->payout_currencies = $payoutCurrencies;
            $payoutMethod->is_active = 1;
            $payoutMethod->is_automatic = 0;
            $payoutMethod->is_sandbox = 0;
            $payoutMethod->environment = 'live';
            $payoutMethod->confirm_payout = 1;
            $payoutMethod->is_auto_update = 0;
            $payoutMethod->currency_type = 0;
            $payoutMethod->logo = $logoPath;
            $payoutMethod->driver = $logoDriver;
            $payoutMethod->save();

            // Keep withdrawal available every day for operations.
            $withdrawalDay = WithdrawalDay::firstOrNew();
            $withdrawalDay->monday = 1;
            $withdrawalDay->tuesday = 1;
            $withdrawalDay->wednesday = 1;
            $withdrawalDay->thursday = 1;
            $withdrawalDay->friday = 1;
            $withdrawalDay->saturday = 1;
            $withdrawalDay->sunday = 1;
            $withdrawalDay->save();
        });

        $this->info('BTC manual-only deposit/withdraw configuration applied successfully.');
        return self::SUCCESS;
    }
}

