@php
    $colors = ['hijau' => '#00bc8c', 'merah' => '#e74c3c', 'putih' => '#f5f5f5', 'merah muda' => '#f4a6c0', 'ungu' => '#8e5ea2'];
    $bg = $colors[$schedule->liturgical_color] ?? '#ffffff';
    $fg = $schedule->liturgical_color === 'putih' || ! $schedule->liturgical_color ? '#1a3d32' : '#ffffff';
    $statusLabels = ['open' => 'Terbuka', 'full' => 'Penuh', 'locked' => 'Terkunci', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan', 'published' => 'Terbit'];
    $status = $schedule->lifecycle_status;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $schedule->title }} – Prodiakon</title>
    <meta name="robots" content="noindex">
    @include('filament.partials.fonts')
    <style>
        body { margin: 0; font-family: Inter, sans-serif; background: #eef5f1; color: #1a3d32; }
        main { max-width: 480px; margin: 0 auto; padding: 24px 16px; }
        .card { background: {{ $bg }}; color: {{ $fg }}; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,.15); }
        h1 { margin: 0 0 12px; font-size: 1.4rem; }
        .row { margin: 6px 0; }
        .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; background: rgba(0,0,0,.2); font-size: .85rem; margin-right: 6px; }
        ul { margin: 6px 0 0; padding-left: 20px; }
        footer { text-align: center; margin-top: 16px; font-size: .8rem; }
        footer a { color: inherit; }
    </style>
</head>
<body>
<main>
    <div class="card">
        <h1>🗓️ {{ $schedule->title }}</h1>
        <div class="row">📍 {{ $schedule->location?->name ?? '-' }}</div>
        <div class="row">Warna Liturgi: {{ $schedule->liturgical_color ? ucfirst($schedule->liturgical_color) : '-' }}</div>
        <div class="row">📅 {{ $schedule->scheduled_date->translatedFormat('D, j M Y') }}</div>
        <div class="row">🕒 {{ date('H:i', strtotime((string) $schedule->start_time)) }}</div>
        <div class="row">
            <span class="badge">{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
            <span class="badge">{{ $schedule->users->count() }} / {{ $schedule->required_personnel }}</span>
        </div>
        <div class="row">
            <strong>Personel</strong>
            @if ($schedule->users->isEmpty())
                <div>Belum ada</div>
            @else
                <ul>@foreach ($schedule->users as $user)<li>{{ $user->name }}</li>@endforeach</ul>
            @endif
        </div>
    </div>
    <footer>@include('filament.partials.footer')</footer>
</main>
</body>
</html>
