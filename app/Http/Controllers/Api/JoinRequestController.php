<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Garden;
use App\Models\JoinRequest;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class JoinRequestController extends Controller
{
    // Fungsi untuk user (Siti) mengirim permintaan bergabung
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'garden_id' => 'required|exists:gardens,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        /** @var \App\Models\User $user */ // <-- PETUNJUK DITAMBAHKAN
        $user = Auth::user();
        $gardenId = $request->garden_id;

        // Cek apakah sudah jadi anggota atau sudah ada permintaan pending
        if (Membership::where('user_id', $user->id)->where('garden_id', $gardenId)->exists() ||
            JoinRequest::where('user_id', $user->id)->where('garden_id', $gardenId)->where('status', 'pending')->exists()) {
            return response()->json(['message' => 'Anda sudah menjadi anggota atau sudah memiliki permintaan tertunda untuk kebun ini.'], 409);
        }

        $joinRequest = JoinRequest::create([
            'user_id' => $user->id,
            'garden_id' => $gardenId,
        ]);

        return response()->json(['message' => 'Permintaan bergabung telah terkirim.', 'data' => $joinRequest], 201);
    }

    // Fungsi untuk pengelola (Budi) melihat daftar permintaan
    public function index()
    {
        /** @var \App\Models\User $user */ // <-- PETUNJUK DITAMBAHKAN
        $user = Auth::user();

        // Garis merah di sini akan hilang
        $managedGardenIds = $user->memberships()->where('role', 'pengelola')->pluck('garden_id');

        $requests = JoinRequest::with(['user:id,nama,email', 'garden:id,nama_kebun'])
                               ->whereIn('garden_id', $managedGardenIds)
                               ->where('status', 'pending')
                               ->get();

        return response()->json($requests);
    }

    // Fungsi untuk pengelola (Budi) menyetujui permintaan
    public function approve(JoinRequest $joinRequest)
    {
        // Otorisasi: Cek apakah user yang login adalah pengelola kebun yang bersangkutan
        if (!Gate::allows('manage-garden', $joinRequest->garden)) {
            return response()->json(['message' => 'Anda tidak punya hak akses untuk mengelola permintaan ini.'], 403);
        }

        if ($joinRequest->status !== 'pending') {
            return response()->json(['message' => 'Permintaan ini sudah direspon.'], 409);
        }
        
        // 1. Tambahkan user ke tabel memberships
        Membership::create([
            'user_id' => $joinRequest->user_id,
            'garden_id' => $joinRequest->garden_id,
            'role' => 'anggota',
        ]);

        // 2. Update status permintaan menjadi 'approved'
        $joinRequest->update(['status' => 'approved']);

        return response()->json(['message' => 'Permintaan bergabung telah disetujui.']);
    }

    // Fungsi untuk pengelola (Budi) menolak permintaan
    public function reject(JoinRequest $joinRequest)
    {
        if (!Gate::allows('manage-garden', $joinRequest->garden)) {
            return response()->json(['message' => 'Anda tidak punya hak akses untuk mengelola permintaan ini.'], 403);
        }

        if ($joinRequest->status !== 'pending') {
            return response()->json(['message' => 'Permintaan ini sudah direspon.'], 409);
        }

        $joinRequest->update(['status' => 'rejected']);

        return response()->json(['message' => 'Permintaan bergabung telah ditolak.']);
    }
}