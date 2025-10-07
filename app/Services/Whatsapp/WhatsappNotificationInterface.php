<?php

namespace App\Services\Whatsapp;

use Illuminate\Http\Request;

interface WhatsappNotificationInterface
{
    /**
     * Kirim pesan WhatsApp
     *
     * @param string $phoneNumber Nomor tujuan (misalnya 0812xxxxxxx)
     * @param string $message Pesan teks
     *
     * @return array hasil normalisasi response (status, code, message, data/error)
     */
    public function sendWhatsAppMessage(
        string $phoneNumber,
        string $message
    ): array;
}
