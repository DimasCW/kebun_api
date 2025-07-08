<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Mencari pengguna berdasarkan nama atau email.
     */
    public function search(Request $request)
    {
        $query = $request->query('query');

        if (strlen($query) < 3) {
            // Jangan lakukan pencarian jika query terlalu pendek
            return response()->json([]);
        }

        $users = User::where('id', '!=', Auth::id()) // Jangan tampilkan diri sendiri
                     ->where(function ($q) use ($query) {
                         $q->where('nama', 'LIKE', "%{$query}%")
                           ->orWhere('email', 'LIKE', "%{$query}%");
                     })
                     ->select('id', 'nama', 'email') // Hanya kirim data yang perlu
                     ->limit(10) // Batasi hasil pencarian
                     ->get();

        return response()->json($users);
    }
}