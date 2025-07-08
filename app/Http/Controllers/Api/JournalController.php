<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\Journal;
use App\Http\Requests\StoreJournalRequest;
use App\Http\Requests\UpdateJournalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\log;
use Illuminate\Support\Facades\Storage;

class JournalController extends Controller
{
    /**
     * Menampilkan daftar jurnal HANYA untuk kebun yang spesifik.
     * Dipanggil oleh: GET /api/gardens/{garden}/journals
     */
    public function index(Request $request, $gardenId) // <-- Perhatikan perubahannya
{
    // Kita cari Garden secara manual di sini
    $garden = Garden::find($gardenId);

    // Jika kebun tidak ditemukan, kirim 404
    if (!$garden) {
        return response()->json(['message' => 'Kebun tidak ditemukan.'], 404);
    }

    // Otorisasi tetap berjalan seperti biasa
    if (Gate::denies('view-garden', $garden)) {
        abort(403, 'Anda bukan anggota dari kebun ini.');
    }

    // Logika query database asli kita kembalikan
    $journals = Journal::with(['author:id,nama', 'plot:id,nama_petak'])
                        ->where('garden_id', $garden->id)
                        ->latest()
                        ->get();

    return response()->json($journals);
}

    /**
     * Menyimpan jurnal baru.
     * Dipanggil oleh: POST /api/journals
     */
    public function store(StoreJournalRequest $request)
    {
        // Validasi dan otorisasi sudah ditangani oleh StoreJournalRequest
        
        $path = null;
        if ($request->hasFile('foto')) {
            // Simpan file di folder 'public/journals'
            $path = $request->file('foto')->store('journals', 'public');
        }

        $journal = Journal::create(array_merge($request->validated(), [
            'foto_url' => $path,
            'penulis_id' => Auth::id(), // Ambil ID user yang sedang login
        ]));

        return response()->json($journal->load(['author:id,nama', 'plot:id,nama_petak']), 201);
    }

    /**
     * Menampilkan detail satu jurnal.
     * Dipanggil oleh: GET /api/journals/{journal}
     */
    public function show(Journal $journal)
    {
        // Otorisasi: Pastikan user adalah anggota dari kebun tempat jurnal ini berada
        if (Gate::denies('view-garden', $journal->garden)) {
            abort(403, 'Anda tidak punya hak akses untuk melihat jurnal ini.');
        }

        return response()->json($journal->load(['author:id,nama', 'plot:id,nama_petak']));
    }

    /**
     * Mengupdate jurnal yang sudah ada.
     * Dipanggil oleh: PATCH /api/journals/{journal}
     */
    public function update(UpdateJournalRequest $request, Journal $journal)
    {
        // Validasi dan otorisasi sudah ditangani oleh UpdateJournalRequest

        $journal->update($request->validated());
        
        return response()->json($journal->load(['author:id,nama', 'plot:id,nama_petak']));
    }

    /**
     * Menghapus sebuah jurnal.
     * Dipanggil oleh: DELETE /api/journals/{journal}
     */
    public function destroy(Journal $journal)
    {
        // Otorisasi: Pastikan user adalah penulis asli dari jurnal ini
        if (Gate::denies('modify-journal', $journal)) {
            abort(403, 'Anda tidak punya hak akses untuk menghapus jurnal ini.');
        }

        // Hapus file foto dari storage jika ada
        if ($journal->foto_url) {
            Storage::disk('public')->delete($journal->foto_url);
        }

        $journal->delete();

        return response()->json(['message' => 'Jurnal berhasil dihapus.']);
    }
}