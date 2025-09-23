<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use AmrShawky\LaravelCurrency\Facade\Currency;

class CurrencyService
{
    public function getRate(string $toCode, ?string $baseCode = null): float
    {
        $base = strtoupper($baseCode ?: config('settings.currency_code'));
        $to = strtoupper($toCode);
        if ($to === $base) {
            return 1.0;
        }

        $cacheKey = "rate_{$base}_{$to}";
        return Cache::remember($cacheKey, now()->addHours(24), function () use ($base, $to) {
            // DB fallback first
            $row = DB::table('currency_rates')
                ->where('base_currency', $base)
                ->where('target_currency', $to)
                ->orderByDesc('fetched_at')
                ->first();
            if ($row && $row->rate > 0) {
                return (float) $row->rate;
            }

            // Provider fetch
            $rate = (float) Currency::convert()->from($base)->to($to)->amount(1)->get();
            // Persist atomically to avoid duplicates under concurrency
            DB::table('currency_rates')->upsert(
                [
                    [
                        'base_currency' => $base,
                        'target_currency' => $to,
                        'rate' => $rate,
                        'fetched_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ],
                ['base_currency', 'target_currency'],
                ['rate', 'fetched_at', 'updated_at']
            );
            return $rate;
        });
    }

    public function convertFromBase(float $amount, string $toCode): float
    {
        $rate = $this->getRate($toCode);
        return $amount * $rate;
    }

    public function convertToBase(float $amount, string $fromCode): float
    {
        $rate = $this->getRate($fromCode);
        $epsilon = 1e-12;
        if (abs($rate) < $epsilon) {
            return $amount;
        }
        return $amount / $rate;
    }
}


