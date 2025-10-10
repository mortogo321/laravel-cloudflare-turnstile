<?php

namespace Mortogo321\LaravelCloudflareTurnstile\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array verify(string $token, string|null $remoteIp = null)
 * @method static string getSiteKey()
 * @method static bool isEnabled()
 *
 * @see \Mortogo321\LaravelCloudflareTurnstile\Services\TurnstileService
 */
class Turnstile extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'turnstile';
    }
}
