<?php

namespace App\Http\Requests;

use App\Models\Garden;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Otorisasi: Izinkan hanya jika user adalah pengelola kebun dari pengumuman ini
        return Gate::allows('manage-garden', $this->route('announcement')->garden);
    }

    public function rules(): array
    {
        return [
            'judul' => 'sometimes|required|string|max:255',
            'isi' => 'sometimes|required|string',
        ];
    }
}