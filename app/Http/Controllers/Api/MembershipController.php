<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

use App\Models\Garden;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // <-- PERUBAHAN 1: TAMBAHKAN IMPORT INI

class MembershipController extends Controller
{
    // Menambahkan seorang anggota ke sebuah kebun (Hanya bisa dilakukan oleh pengelola)
    public function store(Request $request)
    {
        // 1. Validasi input: kita sekarang mengharapkan email
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email', // Cek apakah email ini ada di tabel users
            'garden_id' => 'required|exists:gardens,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $garden = Garden::find($request->garden_id);
        
        // 2. Otorisasi: Pastikan yang mengundang adalah pengelola kebun
        if (Gate::denies('manage-garden', $garden)) {
            return response()->json(['message' => 'Hanya pengelola yang bisa mengundang anggota.'], 403);
        }
        
        // 3. Cari user berdasarkan email yang diinput
        $userToInvite = User::where('email', $request->email)->first();

        // 4. Cek apakah user tersebut sudah menjadi anggota
        $isAlreadyMember = Membership::where('user_id', $userToInvite->id)
                                    ->where('garden_id', $garden->id)
                                    ->exists();
        if ($isAlreadyMember) {
            return response()->json(['message' => 'Pengguna ini sudah menjadi anggota di kebun tersebut.'], 409);
        }

        // 5. Tambahkan user sebagai anggota baru
        $membership = Membership::create([
            'user_id' => $userToInvite->id,
            'garden_id' => $garden->id,
            'role' => 'anggota', // Saat diundang, perannya otomatis anggota
        ]);

        return response()->json($membership, 201);
    }
}