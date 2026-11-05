@php($u = auth()->user())
@if ($u)
    <div class="pk-user">
        <div class="pk-user-row">
            <span class="pk-avatar">{{ \Illuminate\Support\Str::of($u->name)->explode(' ')->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}</span>
            <div class="pk-user-meta">
                <a href="{{ filament()->getProfileUrl() }}"><strong>{{ $u->name }}</strong></a>
                <small>{{ $u->isAdmin() ? 'Administrator' : 'Petugas' }}</small>
            </div>
        </div>
        <form method="post" action="{{ filament()->getLogoutUrl() }}">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </div>
@endif
