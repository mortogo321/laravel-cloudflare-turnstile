<?php

namespace Mortogo321\LaravelCloudflareTurnstile\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    protected string $secretKey;
    protected string $verifyUrl;
    protected Client $client;

    public function __construct(string $secretKey, string $verifyUrl)
    {
        $this->secretKey = $secretKey;
        $this->verifyUrl = $verifyUrl;
        $this->client = new Client([
            'timeout' => config('turnstile.timeout', 30),
        ]);
    }

    /**
     * Verify Turnstile token with Cloudflare.
     *
     * @param string $token
     * @param string|null $remoteIp
     * @return array
     */
    public function verify(string $token, ?string $remoteIp = null): array
    {
        try {
            $response = $this->client->post($this->verifyUrl, [
                'form_params' => [
                    'secret' => $this->secretKey,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return $body ?? ['success' => false];
        } catch (GuzzleException $e) {
            Log::error('Turnstile verification failed', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...',
            ]);

            return [
                'success' => false,
                'error-codes' => ['verification-failed'],
            ];
        }
    }

    /**
     * Get the site key from config.
     *
     * @return string
     */
    public function getSiteKey(): string
    {
        return config('turnstile.site_key', '');
    }

    /**
     * Check if Turnstile is enabled (has valid site key).
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return !empty($this->getSiteKey());
    }
}
