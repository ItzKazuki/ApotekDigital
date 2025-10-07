<?php

namespace App\Services\Whatsapp;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WapiService implements WhatsappNotificationInterface
{
    protected $baseUrl;
    protected $auth;

    const ENDPOINTS = [
        'send_message'  => '/send/message',
    ];

    public function __construct()
    {
        // Ambil dari .env
        $this->baseUrl = env('WAPI_WHATSAPP_URL', 'http://localhost:3000');
        $this->auth    = env('WAPI_WHATSAPP_AUTH', 'user:password'); // format: user:password
    }

    protected function formatPhoneNumber(string $phoneNumber): string
    {
        $defaultCode = env('WAPI_WHATSAPP_DEFAULT_COUNTRY_CODE', '62');

        // Hapus karakter non-digit (misalnya spasi, +, tanda hubung)
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        // Jika mulai dengan 0 → ganti jadi kode negara dari .env
        if (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = $defaultCode . substr($phoneNumber, 1);
        }

        // Jika sudah diawali dengan defaultCode, biarkan
        if (str_starts_with($phoneNumber, $defaultCode)) {
            return $phoneNumber . '@s.whatsapp.net';
        }

        // Jika ada kode negara lain (misal 60, 65), tetap pakai itu
        return $phoneNumber . '@s.whatsapp.net';
    }


    protected function makeRequest($endpoint, $params = [])
    {
        if (!$this->auth) {
            return ['status' => false, 'error' => 'Token belum disetting, kontak admin untuk error di bagian whatsapp token'];
        }

        [$user, $password] = explode(':', $this->auth);

        $response = Http::withBasicAuth($user, $password)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($this->baseUrl . $endpoint, $params);

        $json = $response->json();

        Log::info('WhatsApp Gateway API Response', [
            'endpoint' => $this->baseUrl . $endpoint,
            'response' => $json,
        ]);

        // Jika request HTTP gagal (misalnya 500 / 404)
        if ($response->failed()) {
            return [
                'status' => false,
                'code'   => $json['code'] ?? $response->status(),
                'error'  => $json['message'] ?? 'Unknown error occurred',
            ];
        }

        // Jika response sukses sesuai spec
        if (isset($json['code']) && $json['code'] === 'SUCCESS') {
            return [
                'status'  => true,
                'code'    => $json['code'],
                'message' => $json['message'],
                'data'    => $json['results'],
            ];
        }

        // Kalau code selain SUCCESS → anggap error
        return [
            'status' => false,
            'code'   => $json['code'] ?? $response->status(),
            'error'  => $json['message'] ?? 'Unknown error occurred',
        ];
    }

    public function sendWhatsAppMessage(string $phoneNumber, string $message): array
    {
        return $this->makeRequest(self::ENDPOINTS['send_message'], [
            'phone'       => $this->formatPhoneNumber($phoneNumber),
            'message'     => $message,
            'is_forwarded' => false,
            'duration'    => 0,
        ]);
    }
}
