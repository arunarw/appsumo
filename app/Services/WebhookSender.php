<?php

namespace App\Services;

use App\Models\License;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookSender
{
    public function send(License $license, string $event, ?License $prevLicense = null): void
    {
        $statusMap = [
            'purchase' => 'inactive',
            'activate' => 'active',
            'deactivate' => 'deactivated',
            'upgrade' => 'inactive',
            'downgrade' => 'inactive',
        ];

        $data = [
            'license_key' => $license->license_key,
            'event' => $event,
            'license_status' => $statusMap[$event],
            'event_timestamp' => time(),
            'tier' => $license->tier,
            'test' => false,
        ];

        if ($prevLicense) {
            $data['prev_license_key'] = $prevLicense->license_key;
        }

        $payload = json_encode($data);

        $timestamp = (string) time();
        $secret = config('appsumo.api_key');
        $signature = hash_hmac('sha256', $timestamp . $payload, $secret);

        $url = config('appsumo.webhook_url');

        try {
            $response = Http::withHeaders([
                'X-Appsumo-Signature' => $signature,
                'X-Appsumo-Timestamp' => $timestamp,
                'Content-Type' => 'application/json',
            ])->withBody($payload, 'application/json')->post($url);

            Log::info("Webhook [{$event}] sent to {$url}", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::warning("Webhook [{$event}] failed: {$e->getMessage()}");
        }
    }
}
