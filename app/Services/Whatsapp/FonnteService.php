<?php

namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FonnteService implements WhatsappNotificationInterface
{
    protected $baseUrl;
    protected $auth;

    const ENDPOINTS = [
        'send_message'  => '/send',
    ];

    public function __construct()
    {
        $this->baseUrl = env('FONNTE_WHATSAPP_URL', 'http://localhost:3000');
        $this->auth = env('FONNTE_WHATSAPP_TOKEN');
    }

    protected function makeRequest($endpoint, $params = [])
    {
        $token = $this->auth ?? null;

        if (!$token) {
            return ['status' => false, 'error' => 'API token or device token is required.'];
        }

        // Gunakan JSON format dan pastikan Content-Type header benar
        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type'  => 'application/json', // Tambahkan header
        ])->post($this->baseUrl . $endpoint, $params);

        $json = $response->json();

        // Log respons untuk memudahkan debugging
        Log::info('WhatsApp Gateway API Response', [
            'endpoint' => $this->baseUrl . $endpoint,
            'response' => $json,
        ]);

        if ($response->failed()) {
            return [
                'status' => false,
                'error'  => $response->json()['reason'] ?? 'Unknown error occurred',
            ];
        }

        return [
            'status' => true,
            'data'   => $response->json(),
        ];
    }

    public function sendWhatsAppMessage(string $phoneNumber, string $message): array
    {
        return $this->makeRequest(self::ENDPOINTS['send_message'], [
            'target'  => $phoneNumber,
            'message' => $message,
        ]);
    }
}
