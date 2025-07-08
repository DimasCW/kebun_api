<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\Membership;
use App\Http\Requests\StoreGardenRequest;
use App\Http\Requests\UpdateGardenRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GardenController extends Controller
{
    /**
     * Menampilkan daftar semua kebun untuk publik (fitur "Cari Kebun").
     */
    public function publicIndex()
    {
        $gardens = Garden::select('id', 'nama_kebun', 'alamat')->latest()->get();
        return response()->json($gardens);
    }

    /**
     * Menampilkan daftar kebun di mana user yang login adalah pengelola.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $managedGardenIds = $user->memberships()->where('role', 'pengelola')->pluck('garden_id');

        $gardens = Garden::whereIn('id', $managedGardenIds)->get();
        return response()->json($gardens);
    }

    /**
     * Menyimpan kebun baru dan menjadikan pembuatnya sebagai pengelola.
     */
    public function store(StoreGardenRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $garden = DB::transaction(function () use ($request, $user) {
            $garden = Garden::create(array_merge($request->validated(), [
                'pemilik_id' => $user->id,
            ]));

            Membership::create([
                'user_id' => $user->id,
                'garden_id' => $garden->id,
                'role' => 'pengelola',
            ]);

            return $garden;
        });

        return response()->json($garden->load('owner'), 201);
    }

    /**
     * Menampilkan detail satu kebun.
     */
    public function show(Garden $garden)
    {
        if (! Gate::allows('view-garden', $garden)) {
            abort(403, 'Anda bukan anggota dari kebun ini.');
        }
        return response()->json($garden->load(['owner', 'plots', 'memberships.user']));
    }

    /**
     * Mengupdate detail kebun.
     */
    public function update(UpdateGardenRequest $request, Garden $garden)
    {
        $garden->update($request->validated());
        return response()->json($garden);
    }

    /**
     * Menghapus sebuah kebun.
     */
    public function destroy(Garden $garden)
    {
        if (! Gate::allows('manage-garden', $garden)) {
            abort(403, 'Hanya pengelola yang bisa menghapus kebun ini.');
        }

        $garden->delete();
        return response()->json(['message' => 'Kebun berhasil dihapus.']);
    }
}