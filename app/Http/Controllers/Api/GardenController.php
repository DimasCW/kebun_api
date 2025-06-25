<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;

class GardenController extends Controller
{
    // Menampilkan semua kebun milik user yang sedang login
    public function index()
    {
        $user = Auth::user();
        $gardens = Garden::where('pemilik_id', $user->id)->get();
        return response()->json($gardens);
    }

    // Membuat kebun baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kebun' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Auth::user();

        // Buat kebun baru
        $garden = Garden::create([
            'nama_kebun' => $request->nama_kebun,
            'alamat' => $request->alamat,
            'pemilik_id' => $user->id,
        ]);

        // Secara otomatis, jadikan si pembuat sebagai pengelola kebun tersebut
        Membership::create([
            'user_id' => $user->id,
            'garden_id' => $garden->id,
            'role' => 'pengelola'
        ]);

        return response()->json($garden, 201);
    }

    public function publicIndex()
{
    // Menampilkan daftar semua kebun untuk dipilih
    $gardens = Garden::select('id', 'nama_kebun', 'alamat')->get();
    return response()->json($gardens);
}
public function show(Garden $garden)
    {
        return response()->json($garden);
    }

    // FUNGSI BARU: Mengupdate kebun
    public function update(Request $request, Garden $garden)
    {
        // Otorisasi: Hanya pengelola kebun ini yang boleh mengupdate
        if (! Gate::allows('manage-garden', $garden)) {
            abort(403, 'Anda tidak punya hak akses untuk mengelola kebun ini.');
        }

        $validated = $request->validate([
            'nama_kebun' => 'sometimes|required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $garden->update($validated);

        return response()->json($garden);
    }

    // FUNGSI BARU: Menghapus kebun
    public function destroy(Garden $garden)
    {
        // Otorisasi: Hanya pengelola kebun ini yang boleh menghapus
        if (! Gate::allows('manage-garden', $garden)) {
            abort(403, 'Anda tidak punya hak akses untuk mengelola kebun ini.');
        }

        $garden->delete();

        return response()->json(['message' => 'Kebun berhasil dihapus.']);
    }
}