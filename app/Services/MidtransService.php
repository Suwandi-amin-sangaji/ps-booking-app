<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {

        // $serverKey = config('services.midtrans.server_key');
        // $clientKey = config('services.midtrans.client_key');
        // $isProduction = config('services.midtrans.is_production');

        // Debugging: Tampilkan nilai konfigurasi
        // Log::debug('serverKey: ' . $serverKey);
        // Log::debug('clientKey: ' . $clientKey);
        // Log::debug('isProduction: ' . $isProduction);

        // Set Midtrans configuration
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function getSnapToken($params)
    {
        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }
}
