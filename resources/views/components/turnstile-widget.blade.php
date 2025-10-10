<div class="turnstile-container" data-turnstile-container="{{ $id }}">
    <div id="{{ $id }}"
         class="cf-turnstile"
         data-sitekey="{{ $siteKey }}"
         data-appearance="{{ $appearance }}"
         data-theme="{{ $theme }}"
         data-size="{{ $size }}"
         data-language="{{ $language }}"
         data-retry="{{ $retry }}"
         data-retry-interval="{{ $retryInterval }}"
         @if($action) data-action="{{ $action }}" @endif
         @if($cData) data-cdata="{{ $cData }}" @endif
         data-callback="onTurnstileSuccess_{{ $id }}"
         data-error-callback="onTurnstileError_{{ $id }}"
         data-expired-callback="onTurnstileExpired_{{ $id }}"
         data-timeout-callback="onTurnstileTimeout_{{ $id }}"
         data-before-interactive-callback="onTurnstileBeforeInteractive_{{ $id }}"
         data-after-interactive-callback="onTurnstileAfterInteractive_{{ $id }}"
         data-unsupported-callback="onTurnstileUnsupported_{{ $id }}">
    </div>

    @if($showRefresh)
    <button type="button"
            class="turnstile-refresh-btn"
            id="turnstile-refresh-{{ $id }}"
            style="display: none; margin-top: 10px; padding: 8px 16px; background: #0051c3; color: white; border: none; border-radius: 4px; cursor: pointer;"
            onclick="window.refreshTurnstile_{{ $id }}()">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
            <path d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
            <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
        </svg>
        Retry Verification
    </button>
    @endif
</div>

@once
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=onTurnstileLoad" async defer></script>
<script>
    window.turnstileWidgets = window.turnstileWidgets || {};
    window.turnstileCallbacks = window.turnstileCallbacks || [];

    window.onTurnstileLoad = function() {
        window.turnstileCallbacks.forEach(callback => callback());
        window.turnstileCallbacks = [];
    };

    function executeTurnstileCallback(callback) {
        if (typeof turnstile !== 'undefined') {
            callback();
        } else {
            window.turnstileCallbacks.push(callback);
        }
    }
</script>
@endonce

<script>
(function() {
    const widgetId = '{{ $id }}';
    const buttonSelector = '{{ $buttonSelector }}';
    const disableSubmit = {{ $disableSubmit ? 'true' : 'false' }};
    const showRefresh = {{ $showRefresh ? 'true' : 'false' }};

    let submitButtons = [];
    let widgetReady = false;

    // Find submit buttons
    function findSubmitButtons() {
        const form = document.getElementById(widgetId)?.closest('form');
        if (form) {
            submitButtons = Array.from(form.querySelectorAll(buttonSelector));
            if (submitButtons.length === 0) {
                submitButtons = Array.from(form.querySelectorAll('button[type="submit"]'));
            }
        }
    }

    // Disable/enable submit buttons
    function setSubmitButtonsState(disabled) {
        submitButtons.forEach(button => {
            button.disabled = disabled;
            if (disabled) {
                button.setAttribute('data-turnstile-disabled', 'true');
                button.style.opacity = '0.6';
                button.style.cursor = 'not-allowed';
            } else {
                button.removeAttribute('data-turnstile-disabled');
                button.style.opacity = '1';
                button.style.cursor = 'pointer';
            }
        });
    }

    // Show/hide refresh button
    function showRefreshButton(show) {
        if (showRefresh) {
            const refreshBtn = document.getElementById('turnstile-refresh-' + widgetId);
            if (refreshBtn) {
                refreshBtn.style.display = show ? 'inline-block' : 'none';
            }
        }
    }

    // Callbacks
    window['onTurnstileSuccess_' + widgetId] = function(token) {
        console.log('Turnstile verification successful');
        widgetReady = true;
        if (disableSubmit) {
            setSubmitButtonsState(false);
        }
        showRefreshButton(false);
    };

    window['onTurnstileError_' + widgetId] = function(error) {
        console.error('Turnstile error:', error);
        widgetReady = false;
        if (disableSubmit) {
            setSubmitButtonsState(true);
        }
        showRefreshButton(true);
    };

    window['onTurnstileExpired_' + widgetId] = function() {
        console.warn('Turnstile token expired');
        widgetReady = false;
        if (disableSubmit) {
            setSubmitButtonsState(true);
        }
        showRefreshButton(true);
    };

    window['onTurnstileTimeout_' + widgetId] = function() {
        console.error('Turnstile timeout');
        widgetReady = false;
        if (disableSubmit) {
            setSubmitButtonsState(true);
        }
        showRefreshButton(true);
    };

    window['onTurnstileBeforeInteractive_' + widgetId] = function() {
        console.log('Turnstile before interactive');
    };

    window['onTurnstileAfterInteractive_' + widgetId] = function() {
        console.log('Turnstile after interactive');
    };

    window['onTurnstileUnsupported_' + widgetId] = function() {
        console.error('Turnstile unsupported');
        if (disableSubmit) {
            setSubmitButtonsState(false); // Allow form submission if Turnstile is not supported
        }
    };

    // Refresh function
    window['refreshTurnstile_' + widgetId] = function() {
        const widget = window.turnstileWidgets[widgetId];
        if (widget !== undefined && typeof turnstile !== 'undefined') {
            turnstile.reset(widget);
            showRefreshButton(false);
            if (disableSubmit) {
                setSubmitButtonsState(true);
            }
        }
    };

    // Initialize
    executeTurnstileCallback(function() {
        findSubmitButtons();

        // Disable submit buttons initially if enabled
        if (disableSubmit) {
            setSubmitButtonsState(true);
        }

        // Render widget
        try {
            const widget = turnstile.render('#' + widgetId, {
                sitekey: '{{ $siteKey }}',
                appearance: '{{ $appearance }}',
                theme: '{{ $theme }}',
                size: '{{ $size }}',
                language: '{{ $language }}',
                retry: '{{ $retry }}',
                'retry-interval': {{ $retryInterval }},
                @if($action) action: '{{ $action }}', @endif
                @if($cData) cData: '{{ $cData }}', @endif
                callback: window['onTurnstileSuccess_' + widgetId],
                'error-callback': window['onTurnstileError_' + widgetId],
                'expired-callback': window['onTurnstileExpired_' + widgetId],
                'timeout-callback': window['onTurnstileTimeout_' + widgetId],
                'before-interactive-callback': window['onTurnstileBeforeInteractive_' + widgetId],
                'after-interactive-callback': window['onTurnstileAfterInteractive_' + widgetId],
                'unsupported-callback': window['onTurnstileUnsupported_' + widgetId]
            });

            window.turnstileWidgets[widgetId] = widget;
        } catch (error) {
            console.error('Failed to render Turnstile:', error);
            if (disableSubmit) {
                setSubmitButtonsState(false); // Allow form submission on error
            }
        }
    });
})();
</script>
