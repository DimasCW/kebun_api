<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */ // <-- PETUNJUK UNTUK EDITOR DITAMBAHKAN DI SINI
        $user = Auth::user();

        // Garis merah di sini akan hilang
        $gardenIds = $user->memberships()->pluck('garden_id');

        $schedules = Schedule::whereIn('garden_id', $gardenIds)
                            ->where('status', 'mendatang')
                            ->orderBy('tanggal_kegiatan', 'asc')
                            ->get();

        return response()->json($schedules);
    }

    public function store(StoreScheduleRequest $request)
    {
        $schedule = Schedule::create($request->validated());
        return response()->json($schedule, 201);
    }

    public function show(Schedule $schedule)
    {
        // Bisa ditambahkan Gate untuk memastikan hanya anggota kebun yang bisa lihat
        return response()->json($schedule);
    }

    // Fungsi untuk update status (bukan seluruh data jadwal)
    public function updateStatus(Request $request, Schedule $schedule)
    {
        // Otorisasi menggunakan Gate yang kita buat
        if (! Gate::allows('update-schedule-status', $schedule)) {
            abort(403, 'Anda tidak punya hak akses untuk mengubah status jadwal ini.');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:mendatang,selesai',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $schedule->update(['status' => $request->status]);

        return response()->json($schedule);
    }

    public function destroy(Schedule $schedule)
    {
        // Otorisasi: Hanya pengelola yang boleh menghapus
        if (! Gate::allows('manage-garden', $schedule->garden)) {
            abort(403, 'Hanya pengelola yang bisa menghapus jadwal.');
        }

        $schedule->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }
}