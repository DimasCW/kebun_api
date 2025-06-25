<?php

namespace App\Providers;

use App\Models\Garden;
use App\Models\Journal;
use App\Models\Membership;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate untuk mengecek apakah user adalah PENGELOLA di sebuah kebun
        Gate::define('manage-garden', function (User $user, Garden $garden) {
            return Membership::where('user_id', $user->id)
                             ->where('garden_id', $garden->id)
                             ->where('role', 'pengelola')
                             ->exists();
        });

        // Gate untuk mengecek apakah user adalah PENULIS ASLI sebuah jurnal
        Gate::define('modify-journal', function (User $user, Journal $journal) {
            return $user->id === $journal->penulis_id;
        });
        Gate::define('update-schedule-status', function (User $user, Schedule $schedule) {
            // Cek jika dia pengelola
            if (Gate::forUser($user)->allows('manage-garden', $schedule->garden)) {
                return true;
            }
            // Cek jika namanya ada di daftar penanggung jawab
            return in_array($user->id, $schedule->penanggung_jawab_ids);
        });
    }
}
