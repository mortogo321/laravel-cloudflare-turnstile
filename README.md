# Laravel Cloudflare Turnstile

A Laravel wrapper package for Cloudflare Turnstile with invisible mode by default, automatic submit button disabling until verification is complete, and refresh button on failure.

## Features

- **Invisible Mode by Default**: Uses `execute` appearance mode for seamless user experience
- **Auto-disable Submit Button**: Automatically disables submit buttons until Turnstile is ready
- **Refresh on Failure**: Shows a refresh button when verification fails or times out
- **Easy Integration**: Simple Blade component for quick integration
- **Flexible Validation**: Multiple validation methods (rule class, validation rule string, manual verification)
- **Customizable**: Override all settings per widget or globally
- **Controller-Friendly**: Easy to use in any Laravel controller

## Installation

Install the package via Composer:

```bash
composer require mortogo321/laravel-cloudflare-turnstile
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=turnstile-config
```

Optionally, publish the views if you want to customize them:

```bash
php artisan vendor:publish --tag=turnstile-views
```

## Configuration

Add your Cloudflare Turnstile keys to your `.env` file:

```env
TURNSTILE_SITE_KEY=your-site-key
TURNSTILE_SECRET_KEY=your-secret-key
TURNSTILE_APPEARANCE=execute
TURNSTILE_THEME=auto
```

Get your Turnstile keys from: https://dash.cloudflare.com/?to=/:account/turnstile

### Configuration Options

The package provides several configuration options in `config/turnstile.php`:

- `site_key`: Your Turnstile site key
- `secret_key`: Your Turnstile secret key
- `appearance`: Widget appearance mode (`always`, `execute`, `interaction-only`) - default: `execute`
- `theme`: Widget theme (`light`, `dark`, `auto`) - default: `auto`
- `size`: Widget size (`normal`, `compact`) - default: `normal`
- `language`: Widget language (default: `auto`)
- `retry`: Retry behavior (`auto`, `never`) - default: `auto`
- `retry_interval`: Time between retries in milliseconds - default: `8000`
- `timeout`: Verification timeout in seconds - default: `30`
- `show_refresh_button`: Show refresh button on failure - default: `true`
- `disable_submit_until_ready`: Disable submit button until ready - default: `true`

## Usage

### Basic Usage in Blade Views

Add the Turnstile widget to your form:

```blade
<form method="POST" action="/submit">
    @csrf

    <input type="email" name="email" required>
    <input type="password" name="password" required>

    <!-- Add Turnstile widget -->
    <x-turnstile />

    <!-- Add data-turnstile-button to your submit button -->
    <button type="submit" data-turnstile-button>Submit</button>
</form>
```

### Controller Validation

#### Option 1: Using the Validation Rule Class (Recommended)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mortogo321\LaravelCloudflareTurnstile\Rules\TurnstileRule;

class FormController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
            'cf-turnstile-response' => ['required', new TurnstileRule()],
        ]);

        // Process the form
        return response()->json(['success' => true]);
    }
}
```

#### Option 2: Using the String Validation Rule

```php
public function submit(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:8',
        'cf-turnstile-response' => 'required|turnstile',
    ]);

    // Process the form
    return response()->json(['success' => true]);
}
```

#### Option 3: Manual Verification with Facade

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mortogo321\LaravelCloudflareTurnstile\Facades\Turnstile;

class FormController extends Controller
{
    public function submit(Request $request)
    {
        $token = $request->input('cf-turnstile-response');

        $result = Turnstile::verify($token, $request->ip());

        if (!$result['success']) {
            return back()->withErrors([
                'turnstile' => 'Verification failed. Please try again.'
            ]);
        }

        // Process the form
        return response()->json(['success' => true]);
    }
}
```

#### Option 4: Manual Verification with Service

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mortogo321\LaravelCloudflareTurnstile\Services\TurnstileService;

class FormController extends Controller
{
    public function submit(Request $request, TurnstileService $turnstile)
    {
        $token = $request->input('cf-turnstile-response');

        $result = $turnstile->verify($token, $request->ip());

        if (!$result['success']) {
            return back()->withErrors([
                'turnstile' => 'Verification failed. Please try again.'
            ]);
        }

        // Process the form
        return response()->json(['success' => true]);
    }
}
```

### Customizing the Widget

You can customize the widget behavior using component attributes:

```blade
<x-turnstile
    :appearance="'interaction-only'"
    :theme="'dark'"
    :size="'compact'"
    :show-refresh="true"
    :disable-submit="true"
    :button-selector="'.my-submit-btn'"
    :action="'login'"
    :c-data="json_encode(['user_id' => auth()->id()])"
/>
```

### Custom Submit Button Selector

By default, the package looks for buttons with the `data-turnstile-button` attribute. You can customize this:

```blade
<!-- Using data attribute (default) -->
<button type="submit" data-turnstile-button>Submit</button>

<!-- Or specify a custom selector -->
<x-turnstile :button-selector="'#my-custom-button'" />
<button type="submit" id="my-custom-button">Submit</button>

<!-- Or use a class -->
<x-turnstile :button-selector="'.submit-btn'" />
<button type="submit" class="submit-btn">Submit</button>
```

### Displaying Validation Errors

Display validation errors in your Blade views:

```blade
@error('cf-turnstile-response')
    <div class="alert alert-danger">{{ $message }}</div>
@enderror
```

## How It Works

1. **Widget Loading**: The Turnstile widget loads in invisible mode (`execute` appearance)
2. **Button Disabling**: Submit buttons with `data-turnstile-button` attribute are automatically disabled
3. **Verification**: Turnstile runs automatically when the form is ready
4. **Button Enabling**: Once verification succeeds, submit buttons are re-enabled
5. **Refresh on Failure**: If verification fails or times out, a refresh button appears
6. **Form Submission**: The form submits with the `cf-turnstile-response` token
7. **Server Validation**: Your controller validates the token with Cloudflare

## API Reference

### TurnstileService Methods

```php
// Verify a token
$result = Turnstile::verify(string $token, ?string $remoteIp = null): array;

// Get the site key
$siteKey = Turnstile::getSiteKey(): string;

// Check if Turnstile is enabled
$enabled = Turnstile::isEnabled(): bool;
```

### Verification Response

The `verify()` method returns an array with the following structure:

```php
[
    'success' => true|false,
    'error-codes' => [], // Array of error codes if success is false
    'challenge_ts' => '2024-01-01T00:00:00Z', // Timestamp of the challenge
    'hostname' => 'example.com', // Hostname where the challenge was solved
]
```

## Testing

For testing purposes, you can use Cloudflare's test keys:

- **Site Key**: `1x00000000000000000000AA` (Always passes)
- **Site Key**: `2x00000000000000000000AB` (Always blocks)
- **Site Key**: `3x00000000000000000000FF` (Forces interactive challenge)

## Troubleshooting

### Submit button remains disabled

- Ensure your button has the `data-turnstile-button` attribute or matches your custom selector
- Check browser console for JavaScript errors
- Verify your site key is correct

### Verification fails in controller

- Ensure the `cf-turnstile-response` field is present in the request
- Verify your secret key is correct in `.env`
- Check that the token hasn't expired (tokens are valid for a limited time)

### Widget doesn't appear

- Check that your site key is set in `.env`
- Verify the Cloudflare Turnstile script is loading (check browser network tab)
- Check browser console for errors

## Security

- Never expose your secret key in client-side code
- Always validate on the server side
- Tokens can only be used once and expire after a short time
- Consider rate limiting your forms for additional protection

## License

MIT License

## Credits

- [Cloudflare Turnstile](https://developers.cloudflare.com/turnstile/)
- Built for Laravel

## Support

For issues, questions, or contributions, please visit the GitHub repository.
