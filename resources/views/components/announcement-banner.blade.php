@php
    $announcements = \App\Models\Announcement::activeListCached(3);
@endphp

@if ($announcements->isNotEmpty())
    <div
        id="announcement-stack"
        style="position: fixed; top: 1rem; right: 1rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.75rem; max-width: 28rem; width: calc(100vw - 2rem);"
    >
        @foreach ($announcements as $announcement)
            @php
                $background = $announcement->background_color ?: '#1E88E5';
                $textColor = \App\Support\ColorContrast::textColorForBackground($background);
                $dismissKey = 'announcement_dismissed_' . $announcement->id;
            @endphp

            <div
                class="announcement-toast"
                data-dismiss-key="{{ $dismissKey }}"
                style="background-color: {{ $background }}; color: {{ $textColor }}; padding: 1rem 1.25rem; border-radius: 0.75rem; box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);"
            >
                <div style="line-height: 1.4;">
                    {!! nl2br(e($announcement->message)) !!}
                </div>
                <div style="margin-top: 0.75rem; text-align: right;">
                    <button
                        type="button"
                        data-dismiss-button
                        style="background: transparent; border: none; color: inherit; cursor: pointer; font-weight: 600; text-decoration: underline; padding: 0;"
                    >
                        Close
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        (function () {
            var stack = document.getElementById('announcement-stack');
            if (!stack) return;

            var toasts = stack.querySelectorAll('.announcement-toast');
            toasts.forEach(function (toast) {
                var dismissKey = toast.getAttribute('data-dismiss-key');
                if (dismissKey && sessionStorage.getItem(dismissKey) === '1') {
                    toast.remove();
                    return;
                }

                var button = toast.querySelector('[data-dismiss-button]');
                if (!button) return;

                button.addEventListener('click', function () {
                    if (dismissKey) {
                        sessionStorage.setItem(dismissKey, '1');
                    }
                    toast.remove();
                    if (stack.querySelectorAll('.announcement-toast').length === 0) {
                        stack.remove();
                    }
                });
            });
        })();
    </script>
@endif
