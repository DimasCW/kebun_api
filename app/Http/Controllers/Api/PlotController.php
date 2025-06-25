<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlotController extends Controller
{
    // Membuat petak baru di sebuah kebun
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'garden_id' => 'required|exists:gardens,id',
            'nama_petak' => 'required|string|max:255',
            'pemilik_id' => 'nullable|exists:users,id', // ID user anggota
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        // Disini bisa ditambahkan validasi apakah user yang request adalah pengelola kebun

        $plot = Plot::create($request->all());

        return response()->json($plot, 201);
    }
}