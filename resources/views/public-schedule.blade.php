@php
    // Warna latar sama dengan card dashboard (theme.css). Warna teks dipilih
    // supaya kontras >= 4.5:1 (hijau & merah di dashboard pakai teks putih, kontrasnya kurang).
    $palette = [
        'hijau' => ['#00bc8c', '#111111'],
        'merah' => ['#e74c3c', '#111111'],
        'putih' => ['#ffffff', '#111111'],
        'merah muda' => ['#ff6cb8', '#111111'],
        'ungu' => ['#815e92', '#ffffff'],
    ];
    [$bg, $fg] = $palette[$schedule->liturgical_color] ?? ['#ffffff', '#111111'];
    $status = $schedule->lifecycle_status;
    $statusLabels = ['open' => 'Terbuka', 'full' => 'Penuh', 'locked' => 'Terkunci', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan', 'published' => 'Terbit'];
    $statusTone = ['open' => 'success', 'full' => 'warning'][$status] ?? 'gray';
    $capacityTone = $status === 'full' ? 'warning' : 'success';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $schedule->title }} – Prodiakon</title>
    <meta name="robots" content="noindex">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --pk-green: #1f4a3d; --pk-cream: #f7f6f2; --pk-line: #e6e3da; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, ui-sans-serif, system-ui, sans-serif; background: var(--pk-cream); color: #111; -webkit-font-smoothing: antialiased; }
        main { max-width: 480px; margin: 0 auto; padding: 24px 16px 8px; }
        .card { background: {{ $bg }}; color: {{ $fg }}; border-radius: .75rem; padding: 1.25rem; box-shadow: 0 0 0 1px rgba(0,0,0,.1), 0 1px 2px rgba(0,0,0,.06); }
        h1 { margin: 0 0 .75rem; font-size: 1.125rem; line-height: 1.75rem; font-weight: 800; }
        .row { display: flex; align-items: center; gap: .375rem; margin-top: .5rem; font-size: .875rem; line-height: 1.25rem; font-weight: 500; }
        .row svg { flex: none; width: 1.25rem; height: 1.25rem; }
        .small { font-size: .75rem; line-height: 1rem; font-weight: 400; }
        .badges { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .75rem; }
        .badge { padding: .125rem .5rem; border-radius: .375rem; font-size: .75rem; line-height: 1rem; font-weight: 500; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-gray { background: #e5e7eb; color: #374151; }
        .panel { margin-top: .75rem; padding: .75rem; border-radius: .5rem; background: rgba(255,255,255,.92); color: #111; font-size: .875rem; }
        .panel b { display: block; font-weight: 600; margin-bottom: .25rem; }
        .panel ul { margin: 0; padding-left: 1.25rem; }
        .pk-footer { padding: 1.5rem 1rem; text-align: center; font-size: .75rem; color: #5c5c55; margin: 0; }
        .pk-footer a { color: var(--pk-green); font-weight: 500; text-decoration: underline; }
    </style>
</head>
<body>
<main>
    <article class="card">
        <h1>🗓️ {{ $schedule->title }}</h1>
        <div class="row"><x-heroicon-o-map-pin />{{ $schedule->location?->name ?? '-' }}</div>
        <div class="row small">Warna Liturgi: {{ $schedule->liturgical_color ? ucfirst($schedule->liturgical_color) : '-' }}</div>
        <div class="row"><x-heroicon-o-calendar-days />{{ $schedule->scheduled_date->translatedFormat('D, j M Y') }}</div>
        <div class="row"><x-heroicon-o-clock />{{ date('H:i', strtotime((string) $schedule->start_time)) }}</div>
        <div class="badges">
            <span class="badge badge-{{ $statusTone }}">{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
            <span class="badge badge-{{ $capacityTone }}">{{ $schedule->users->count() }} / {{ $schedule->required_personnel }}</span>
        </div>
        <div class="panel">
            <b>Personnel</b>
            @if ($schedule->users->isEmpty())
                None assigned
            @else
                <ul>@foreach ($schedule->users as $user)<li>{{ $user->name }}</li>@endforeach</ul>
            @endif
        </div>
    </article>
    @include('filament.partials.footer')
</main>
</body>
</html>
