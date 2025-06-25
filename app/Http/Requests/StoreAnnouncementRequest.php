<?php

namespace App\Http\Requests;

use App\Models\Garden;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreAnnouncementRequest extends FormRequest
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
            'isi' => 'required|string',
        ];
    }
}