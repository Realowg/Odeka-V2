<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
      // Payment webhooks - specific endpoints only
      'stripe/webhook',
      'webhook/paypal',
      'webhook/paystack', 
      'webhook/ccbill',
      'webhook/mollie',
      'webhook/cardinity',
      'webhook/cardinity/cancel',
      'webhook/nowpayments',
      'webhook/payku',
      'webhook/coinbase',
      'webhook/binance',
      'coinpayments/ipn',
      
      // CCBill approval (legacy)
      'ccbill/approved',
      
      // Storage webhooks (video encoding)
      'webhook/storage/*',
      'webhook/coco/*',
      'webhook/message/coco/*',
      'webhook/welcome/message/coco/*',
      'webhook/story/coco/*'
    ];
}
