<?php

namespace Mortogo321\LaravelCloudflareTurnstile\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Mortogo321\LaravelCloudflareTurnstile\Services\TurnstileService;

class TurnstileRule implements ValidationRule
{
    protected ?string $remoteIp;

    public function __construct(?string $remoteIp = null)
    {
        $this->remoteIp = $remoteIp ?? request()->ip();
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            $fail('The Turnstile verification is required.');
            return;
        }

        $turnstile = app(TurnstileService::class);
        $result = $turnstile->verify($value, $this->remoteIp);

        if (!($result['success'] ?? false)) {
            $errorCodes = $result['error-codes'] ?? [];
            $message = $this->getErrorMessage($errorCodes);
            $fail($message);
        }
    }

    /**
     * Get user-friendly error message based on error codes.
     */
    protected function getErrorMessage(array $errorCodes): string
    {
        if (empty($errorCodes)) {
            return 'The Turnstile verification failed. Please try again.';
        }

        $errorMessages = [
            'missing-input-secret' => 'Turnstile configuration error: missing secret key.',
            'invalid-input-secret' => 'Turnstile configuration error: invalid secret key.',
            'missing-input-response' => 'The Turnstile verification is required.',
            'invalid-input-response' => 'The Turnstile verification token is invalid.',
            'bad-request' => 'The Turnstile request was malformed.',
            'timeout-or-duplicate' => 'The Turnstile token has expired or was already used.',
            'internal-error' => 'An internal error occurred during verification.',
        ];

        $firstError = $errorCodes[0];
        return $errorMessages[$firstError] ?? 'The Turnstile verification failed. Please try again.';
    }
}
