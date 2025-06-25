<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest; // <-- Tambahkan
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate; // <-- Tambahkan

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */ // <-- PETUNJUK UNTUK EDITOR
        $user = Auth::user();
        
        // Garis merah di sini akan hilang
        $gardenIds = $user->memberships()->pluck('garden_id');

        $announcements = Announcement::with('author:id,nama')->whereIn('garden_id', $gardenIds)->latest()->get();

        return response()->json($announcements);
    }

    public function store(StoreAnnouncementRequest $request)
    {
        $announcement = Announcement::create(array_merge($request->validated(), [
            'penulis_id' => Auth::id()
        ]));

        return response()->json($announcement, 201);
    }

    public function show(Announcement $announcement)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Otorisasi: Pastikan user adalah anggota kebun dari pengumuman ini
        if (!$user->memberships()->where('garden_id', $announcement->garden_id)->exists()) {
            abort(403, 'Anda tidak punya hak akses untuk melihat pengumuman ini.');
        }

        return response()->json($announcement->load('author:id,nama'));
    }

    // FUNGSI BARU: Mengupdate pengumuman
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement)
    {
        $announcement->update($request->validated());
        return response()->json($announcement);
    }

    // FUNGSI BARU: Menghapus pengumuman
    public function destroy(Announcement $announcement)
    {
        // Otorisasi: Hanya pengelola yang boleh menghapus
        if (!Gate::allows('manage-garden', $announcement->garden)) {
            abort(403, 'Hanya pengelola yang bisa menghapus pengumuman.');
        }

        $announcement->delete();
        return response()->json(['message' => 'Pengumuman berhasil dihapus.']);
    }
}