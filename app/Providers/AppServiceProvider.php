<?php

namespace App\Providers;

use App\Models\JadwalAgenda;
use App\Models\JadwalMk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer('partials.navbar', function ($view) {
            $today = Carbon::today();
            $todayDayName = $today->locale('id')->translatedFormat('l');

            $todaySchedules = collect();
            $upcomingAgendas = collect();

            if (Schema::hasTable('jadwal_mk')) {
                $todaySchedules = JadwalMk::with(['mataKuliah', 'dosenPengampu', 'ruanganLokasi'])
                    ->where(function ($q) use ($todayDayName, $today) {
                        $q->where(function ($reg) use ($todayDayName) {
                            $reg->where('is_pengganti', false)->where('hari', $todayDayName);
                        })->orWhere(function ($sub) use ($today) {
                            $sub->where('is_pengganti', true)->whereDate('tanggal', $today);
                        });
                    })
                    ->orderBy('jam_mulai')
                    ->get();
            }

            if (Schema::hasTable('jadwal_agenda')) {
                $upcomingAgendas = JadwalAgenda::with(['agendaNonAkademik', 'ruanganLokasi'])
                    ->whereDate('tanggal', '>=', $today)
                    ->orderBy('tanggal')
                    ->orderBy('jam_mulai')
                    ->take(5)
                    ->get();
            }

            $view->with([
                'todaySchedules' => $todaySchedules,
                'upcomingAgendas' => $upcomingAgendas,
                'totalReminders' => $todaySchedules->count() + $upcomingAgendas->count(),
                'systemToday' => $today,
            ]);
        });
    }
}
