<?php

namespace App\Http\Requests;

use App\Models\Garden;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $garden = Garden::find($this->garden_id);
        return $garden && Gate::allows('manage-garden', $garden);
    }

    public function rules(): array
    {
        return [
            'garden_id' => 'required|exists:gardens,id',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_kegiatan' => 'required|date',
            'penanggung_jawab_ids' => 'required|array',
            'penanggung_jawab_ids.*' => 'exists:users,id', // Cek setiap id ada di tabel users
        ];
    }
}