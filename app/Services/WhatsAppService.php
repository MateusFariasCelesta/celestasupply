<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private string $token;
    private bool   $enabled;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.whatsapp.url', ''), '/');
        $this->token   = (string) config('services.whatsapp.token', '');
        $this->enabled = !empty($this->baseUrl);
    }

    public function send(string $phone, string $message): bool
    {
        if (!$this->enabled || !$phone) return false;

        try {
            $response = Http::timeout(10)
                ->withToken($this->token)
                ->post("{$this->baseUrl}/send", [
                    'to'      => preg_replace('/\D/', '', $phone),
                    'message' => $message,
                ]);

            if (!$response->successful()) {
                Log::warning('WhatsApp send failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'phone'  => $phone,
                ]);
            }

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning('WhatsApp service unreachable: ' . $e->getMessage());
            return false;
        }
    }
}
