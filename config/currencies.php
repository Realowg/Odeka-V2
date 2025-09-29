<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Zero-decimal currencies
    |--------------------------------------------------------------------------
    */

    'zero-decimal' => [
        'BIF',
        'CLP',
        'DJF',
        'GNF',
        'JPY',
        'KMF',
        'KRW',
        'MGA',
        'PYG',
        'RWF',
        'UGX',
        'VND',
        'VUV',
        'XAF',
        'XOF',
        'XPF',        
    ],

    // Optional: explicit decimals map for special currencies
    'decimals' => [
        'USD' => 2,
        'EUR' => 2,
        'GBP' => 2,
        'XOF' => 0,
        'XAF' => 0,
        'JPY' => 0,
    ],

    // Supported currencies (code => label)
    'supported' => [
        'USD' => 'US Dollar',
        'EUR' => 'Euro',
        'GBP' => 'British Pound',
        'XOF' => 'West African CFA',
        'XAF' => 'Central African CFA',
        'JPY' => 'Japanese Yen',
    ],

    // Symbols for supported currencies
    'symbols' => [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'XOF' => 'CFA',
        'XAF' => 'FCFA',
        'JPY' => '¥',
    ],
];
