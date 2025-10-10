<?php

namespace Mortogo321\LaravelCloudflareTurnstile\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Mortogo321\LaravelCloudflareTurnstile\Services\TurnstileService;

class TurnstileWidget extends Component
{
    public string $siteKey;
    public string $appearance;
    public string $theme;
    public string $size;
    public string $language;
    public string $retry;
    public int $retryInterval;
    public bool $showRefresh;
    public bool $disableSubmit;
    public string $action;
    public string $cData;
    public ?string $id;
    public ?string $buttonSelector;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $appearance = null,
        ?string $theme = null,
        ?string $size = null,
        ?string $language = null,
        ?string $retry = null,
        ?int $retryInterval = null,
        ?bool $showRefresh = null,
        ?bool $disableSubmit = null,
        ?string $action = null,
        ?string $cData = null,
        ?string $id = null,
        ?string $buttonSelector = null
    ) {
        $turnstile = app(TurnstileService::class);

        $this->siteKey = $turnstile->getSiteKey();
        $this->appearance = $appearance ?? config('turnstile.appearance', 'execute');
        $this->theme = $theme ?? config('turnstile.theme', 'auto');
        $this->size = $size ?? config('turnstile.size', 'normal');
        $this->language = $language ?? config('turnstile.language', 'auto');
        $this->retry = $retry ?? config('turnstile.retry', 'auto');
        $this->retryInterval = $retryInterval ?? config('turnstile.retry_interval', 8000);
        $this->showRefresh = $showRefresh ?? config('turnstile.show_refresh_button', true);
        $this->disableSubmit = $disableSubmit ?? config('turnstile.disable_submit_until_ready', true);
        $this->action = $action ?? '';
        $this->cData = $cData ?? '';
        $this->id = $id ?? 'turnstile-' . uniqid();
        $this->buttonSelector = $buttonSelector ?? '[data-turnstile-button]';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('turnstile::components.turnstile-widget');
    }
}
