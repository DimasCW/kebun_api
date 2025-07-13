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
use Illuminate\Support\Facades\Log; // Perbaikan: 'L' huruf besar
use Illuminate\Support\Facades\Storage;

class JournalController extends Controller
{
    /**
     * Menampilkan daftar jurnal HANYA untuk kebun yang spesifik.
     */
    public function index(Request $request, $gardenId)
    {
        $garden = Garden::find($gardenId);

        if (!$garden) {
            return response()->json(['message' => 'Kebun tidak ditemukan.'], 404);
        }

        if (Gate::denies('view-garden', $garden)) {
            abort(403, 'Anda bukan anggota dari kebun ini.');
        }

        // Perbaikan: Pastikan 'email' disertakan di sini
        $journals = Journal::with(['author:id,nama,email', 'plot:id,nama_petak'])
                            ->where('garden_id', $garden->id)
                            ->latest()
                            ->get();

        return response()->json($journals);
    }

    /**
     * Menyimpan jurnal baru.
     */
    public function store(StoreJournalRequest $request)
    {
        $path = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('journals', 'public');
        }

        $journal = Journal::create(array_merge($request->validated(), [
            'foto_url' => $path,
            'penulis_id' => Auth::id(),
        ]));

        // Perbaikan: Pastikan 'email' disertakan di sini
        $newJournal = Journal::with(['author:id,nama,email', 'plot:id,nama_petak'])
                              ->find($journal->id);

        return response()->json($newJournal, 201);
    }

    /**
     * Menampilkan detail satu jurnal.
     */
    public function show(Journal $journal)
    {
        if (Gate::denies('view-garden', $journal->garden)) {
            abort(403, 'Anda tidak punya hak akses untuk melihat jurnal ini.');
        }

        // Perbaikan: Pastikan 'email' disertakan di sini
        return response()->json($journal->load(['author:id,nama,email', 'plot:id,nama_petak']));
    }

    /**
     * Mengupdate jurnal yang sudah ada.
     */
    public function update(UpdateJournalRequest $request, Journal $journal)
    {
        $journal->update($request->validated());
        
        // Perbaikan: Pastikan 'email' disertakan di sini
        return response()->json($journal->load(['author:id,nama,email', 'plot:id,nama_petak']));
    }

    /**
     * Menghapus sebuah jurnal.
     */
    public function destroy(Journal $journal)
    {
        if (Gate::denies('modify-journal', $journal)) {
            abort(403, 'Anda tidak punya hak akses untuk menghapus jurnal ini.');
        }

        if ($journal->foto_url) {
            Storage::disk('public')->delete($journal->foto_url);
        }

        $journal->delete();

        return response()->json(['message' => 'Jurnal berhasil dihapus.']);
    }
}
