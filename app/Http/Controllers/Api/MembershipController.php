<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // <-- PERUBAHAN 1: TAMBAHKAN IMPORT INI

class MembershipController extends Controller
{
    // Menambahkan seorang anggota ke sebuah kebun (Hanya bisa dilakukan oleh pengelola)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'garden_id' => 'required|exists:gardens,id',
            'role' => 'required|in:anggota,pengelola',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cek apakah user yang request adalah pengelola kebun target
        // PERUBAHAN 2: Ganti auth()->id() menjadi Auth::id()
        $isManager = Membership::where('user_id', Auth::id()) 
                                ->where('garden_id', $request->garden_id)
                                ->where('role', 'pengelola')
                                ->exists();

        if (!$isManager) {
            return response()->json(['message' => 'Anda tidak punya hak akses untuk menambahkan anggota di kebun ini.'], 403);
        }

        // Cek apakah user sudah menjadi anggota
        $isAlreadyMember = Membership::where('user_id', $request->user_id)
                                     ->where('garden_id', $request->garden_id)
                                     ->exists();

        if ($isAlreadyMember) {
            return response()->json(['message' => 'User ini sudah menjadi anggota di kebun tersebut.'], 409);
        }

        $membership = Membership::create([
            'user_id' => $request->user_id,
            'garden_id' => $request->garden_id,
            'role' => $request->role,
        ]);

        return response()->json($membership, 201);
    }
}