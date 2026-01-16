@php
    $announcement = \App\Models\Announcement::activeCached();
@endphp

@if ($announcement)
    @php
        $background = $announcement->background_color ?: '#1E88E5';
        $textColor = \App\Support\ColorContrast::textColorForBackground($background);
    @endphp

    <div style="background-color: {{ $background }}; color: {{ $textColor }}; padding: 0.75rem 1rem; text-align: center; width: 100%;">
        <div style="max-width: 72rem; margin: 0 auto;">
            {!! nl2br(e($announcement->message)) !!}
        </div>
    </div>
@endif
