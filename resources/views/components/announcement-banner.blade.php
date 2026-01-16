@php
    $announcement = \App\Models\Announcement::activeCached();
@endphp

@if ($announcement)
    @php
        $background = $announcement->background_color ?: '#1E88E5';
        $textColor = \App\Support\ColorContrast::textColorForBackground($background);
        $dismissKey = 'announcement_dismissed_' . $announcement->id;
    @endphp

    <div
        id="announcement-banner"
        data-dismiss-key="{{ $dismissKey }}"
        style="position: fixed; top: 1rem; right: 1rem; z-index: 9999; max-width: 28rem; width: calc(100vw - 2rem); background-color: {{ $background }}; color: {{ $textColor }}; padding: 1rem 1.25rem; border-radius: 0.75rem; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);"
    >
        <div style="display: flex; align-items: start; gap: 0.75rem;">
            <div style="flex: 1; line-height: 1.4;">
                {!! nl2br(e($announcement->message)) !!}
            </div>
            <button
                type="button"
                aria-label="Dismiss announcement"
                data-dismiss-button
                style="background: transparent; border: none; color: inherit; cursor: pointer; font-size: 1.25rem; line-height: 1; padding: 0;"
            >
                ×
            </button>
        </div>
    </div>

    <script>
        (function () {
            var banner = document.getElementById('announcement-banner');
            if (!banner) return;

            var dismissKey = banner.getAttribute('data-dismiss-key');
            if (dismissKey && sessionStorage.getItem(dismissKey) === '1') {
                banner.remove();
                return;
            }

            var button = banner.querySelector('[data-dismiss-button]');
            if (!button) return;

            button.addEventListener('click', function () {
                if (dismissKey) {
                    sessionStorage.setItem(dismissKey, '1');
                }
                banner.remove();
            });
        })();
    </script>
@endif
