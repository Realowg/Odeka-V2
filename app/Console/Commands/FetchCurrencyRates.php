<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use AmrShawky\LaravelCurrency\Facade\Currency;

class FetchCurrencyRates extends Command
{
    protected $signature = 'currency:fetch {base?}';
    protected $description = 'Fetch and store latest currency rates';

    public function handle(): int
    {
        $baseArg = $this->argument('base');
        $base = $baseArg ? strtoupper($baseArg) : null;
        if (! $base) {
            // Try admin settings table
            try {
                $settings = \App\Models\AdminSettings::first();
                $base = strtoupper($settings?->currency_code ?? 'USD');
            } catch (\Throwable $e) {
                $base = 'USD';
            }
        }
        $this->info("Fetching rates base={$base}");
        try {
            $rates = Currency::rates()->latest()->base($base)->get();
            $now = now();
            if (is_array($rates) || is_object($rates)) {
                foreach ($rates as $code => $rate) {
                    if ($code === $base) continue;
                    DB::table('currency_rates')->updateOrInsert(
                        ['base_currency' => $base, 'target_currency' => $code],
                        ['rate' => (float) $rate, 'fetched_at' => $now, 'updated_at' => $now, 'created_at' => $now]
                    );
                }
            } else {
                // Fallback: convert individually for supported currencies
                $supported = config('currencies.supported') ?? [];
                foreach ($supported as $k => $v) {
                    $code = is_numeric($k) ? $v : $k;
                    if (strtoupper($code) === $base) continue;
                    $rate = (float) Currency::convert()->from($base)->to($code)->amount(1)->get();
                    DB::table('currency_rates')->updateOrInsert(
                        ['base_currency' => $base, 'target_currency' => $code],
                        ['rate' => (float) $rate, 'fetched_at' => $now, 'updated_at' => $now, 'created_at' => $now]
                    );
                }
            }
            $this->info('Rates updated.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}


