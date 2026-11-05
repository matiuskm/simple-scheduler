<x-filament-widgets::widget>
    <section class="pk-hero">
        <p class="pk-eyebrow">{{ $today }}</p>
        <h1>Selamat datang, <em>{{ $firstName }}</em></h1>
        <p class="pk-lead">Kelola jadwal perayaan dan pelayanan prodiakon paroki dalam satu tempat.</p>
        <div class="pk-stats">
            <div><b>{{ $upcoming }}</b><span>Jadwal mendatang</span></div>
            <div><b>{{ $mine }}</b><span>Jadwal saya</span></div>
            <div><b>{{ $open }}</b><span>Masih terbuka</span></div>
        </div>
    </section>
</x-filament-widgets::widget>
