<?php
// app/Http/Requests/UpdateJournalRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Izinkan request jika user adalah penulis asli jurnal ini (menggunakan Gate)
        return Gate::allows('modify-journal', $this->route('journal'));
    }

    public function rules(): array
    {
        return [
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string',
        ];
    }
}