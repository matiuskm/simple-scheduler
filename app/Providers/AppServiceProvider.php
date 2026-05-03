<?php

namespace App\Providers;

use App\Models\ActivityLog;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // No bindings needed
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn(): View => view('partials.ga'),
        );

        Event::listen(Login::class, function (Login $event): void {
            $event->user->timestamps = false;
            $event->user->forceFill(['last_login_at' => now()])->save();
            $event->user->timestamps = true;

            ActivityLog::create([
                'subject_type' => get_class($event->user),
                'subject_id'   => $event->user->getKey(),
                'actor_id'     => $event->user->getKey(),
                'action'       => 'login',
                'ip_address'   => request()->ip(),
            ]);
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if (! $event->user) {
                return;
            }

            ActivityLog::create([
                'subject_type' => get_class($event->user),
                'subject_id'   => $event->user->getKey(),
                'actor_id'     => $event->user->getKey(),
                'action'       => 'logout',
                'ip_address'   => request()->ip(),
            ]);
        });
    }
}
