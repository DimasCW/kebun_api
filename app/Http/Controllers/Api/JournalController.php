<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreJournalRequest; 
use App\Http\Requests\UpdateJournalRequest; 

class JournalController extends Controller
{
    /**
     * Menampilkan daftar jurnal dari kebun tempat user menjadi anggota
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */ // <-- PETUNJUK UNTUK EDITOR
        $user = Auth::user();
        
        // Garis merah di sini akan hilang
        $gardenIds = $user->memberships()->pluck('garden_id');

        // Ambil semua jurnal dari kebun-kebun tersebut
        $journals = Journal::whereIn('garden_id', $gardenIds)->latest()->get();

        return response()->json($journals);
    }

    /**
     * Menyimpan jurnal baru
     */
    // FUNGSI YANG DIPERBARUI: Lebih bersih
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

        return response()->json($journal, 201);
    }

    // FUNGSI BARU: Menampilkan detail satu jurnal
    public function show(Journal $journal)
    {
        return response()->json($journal->load(['author:id,nama', 'plot:id,nama_petak']));
    }

    // FUNGSI BARU: Mengupdate jurnal
    public function update(UpdateJournalRequest $request, Journal $journal)
    {
        $journal->update($request->validated());
        return response()->json($journal);
    }

    // FUNGSI BARU: Menghapus jurnal
    public function destroy(Journal $journal)
    {
        // Otorisasi
        if (! Gate::allows('modify-journal', $journal)) {
            abort(403, 'Anda tidak punya hak akses untuk menghapus jurnal ini.');
        }
        
        // Hapus juga fotonya jika ada
        // if ($journal->foto_url) { Storage::disk('public')->delete($journal->foto_url); }
        
        $journal->delete();

        return response()->json(['message' => 'Jurnal berhasil dihapus.']);
    }
}