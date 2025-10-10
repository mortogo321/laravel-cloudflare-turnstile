<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile Site Key
    |--------------------------------------------------------------------------
    |
    | This is your Turnstile site key from Cloudflare dashboard.
    | Get your keys at: https://dash.cloudflare.com/?to=/:account/turnstile
    |
    */
    'site_key' => env('TURNSTILE_SITE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile Secret Key
    |--------------------------------------------------------------------------
    |
    | This is your Turnstile secret key from Cloudflare dashboard.
    |
    */
    'secret_key' => env('TURNSTILE_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Turnstile Verification URL
    |--------------------------------------------------------------------------
    |
    | The URL to verify the Turnstile token with Cloudflare.
    |
    */
    'verify_url' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',

    /*
    |--------------------------------------------------------------------------
    | Default Appearance Mode
    |--------------------------------------------------------------------------
    |
    | The default appearance mode for Turnstile.
    | Options: 'always', 'execute', 'interaction-only'
    | Default: 'execute' (invisible mode)
    |
    */
    'appearance' => env('TURNSTILE_APPEARANCE', 'execute'),

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | The theme for Turnstile widget.
    | Options: 'light', 'dark', 'auto'
    |
    */
    'theme' => env('TURNSTILE_THEME', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Size
    |--------------------------------------------------------------------------
    |
    | The size of the widget.
    | Options: 'normal', 'compact'
    |
    */
    'size' => env('TURNSTILE_SIZE', 'normal'),

    /*
    |--------------------------------------------------------------------------
    | Language
    |--------------------------------------------------------------------------
    |
    | The language to use for the widget.
    | Uses auto-detection if not specified.
    |
    */
    'language' => env('TURNSTILE_LANGUAGE', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Retry
    |--------------------------------------------------------------------------
    |
    | Whether to retry on failure.
    | Options: 'auto', 'never'
    |
    */
    'retry' => env('TURNSTILE_RETRY', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Retry Interval
    |--------------------------------------------------------------------------
    |
    | Time in milliseconds between retry attempts.
    |
    */
    'retry_interval' => env('TURNSTILE_RETRY_INTERVAL', 8000),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Verification timeout in seconds.
    |
    */
    'timeout' => env('TURNSTILE_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Show Refresh Button
    |--------------------------------------------------------------------------
    |
    | Show refresh icon button when loading fails.
    |
    */
    'show_refresh_button' => env('TURNSTILE_SHOW_REFRESH', true),

    /*
    |--------------------------------------------------------------------------
    | Disable Submit Button
    |--------------------------------------------------------------------------
    |
    | Disable submit button until Turnstile is ready.
    | Uses data-turnstile-button attribute on button element.
    |
    */
    'disable_submit_until_ready' => env('TURNSTILE_DISABLE_SUBMIT', true),
];
