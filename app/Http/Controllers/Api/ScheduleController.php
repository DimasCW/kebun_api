<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal untuk kebun yang spesifik.
     * Dipanggil oleh: GET /api/gardens/{garden}/schedules
     */
    public function index(Garden $garden)
    {
        // Otorisasi: Pastikan user yang login adalah anggota dari kebun ini
        if (Gate::denies('view-garden', $garden)) {
            abort(403, 'Anda bukan anggota dari kebun ini.');
        }

        $schedules = Schedule::where('garden_id', $garden->id)
                            ->orderBy('tanggal_kegiatan', 'asc')
                            ->get();

        return response()->json($schedules);
    }

    /**
     * Menyimpan jadwal baru.
     * Dipanggil oleh: POST /api/schedules
     */
    public function store(StoreScheduleRequest $request)
    {
        // Validasi dan otorisasi sudah ditangani oleh StoreScheduleRequest
        $schedule = Schedule::create($request->validated());
        return response()->json($schedule, 201);
    }

    /**
     * Menampilkan detail satu jadwal.
     * Dipanggil oleh: GET /api/schedules/{schedule}
     */
    public function show(Schedule $schedule)
    {
        // Otorisasi: Pastikan user adalah anggota dari kebun tempat jadwal ini berada
        if (Gate::denies('view-garden', $schedule->garden)) {
            abort(403, 'Anda tidak punya hak akses untuk melihat jadwal ini.');
        }

        return response()->json($schedule);
    }

    /**
     * Mengupdate status sebuah jadwal (misal: dari 'mendatang' ke 'selesai').
     * Dipanggil oleh: PATCH /api/schedules/{schedule}/status
     */
    public function updateStatus(Request $request, Schedule $schedule)
    {
        // Otorisasi menggunakan Gate yang kita buat di AppServiceProvider
        if (Gate::denies('update-schedule-status', $schedule)) {
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

    /**
     * Menghapus sebuah jadwal.
     * Dipanggil oleh: DELETE /api/schedules/{schedule}
     */
    public function destroy(Schedule $schedule)
    {
        // Otorisasi: Hanya pengelola yang boleh menghapus
        if (Gate::denies('manage-garden', $schedule->garden)) {
            abort(403, 'Hanya pengelola yang bisa menghapus jadwal.');
        }

        $schedule->delete();
        return response()->json(['message' => 'Jadwal berhasil dihapus.']);
    }
}