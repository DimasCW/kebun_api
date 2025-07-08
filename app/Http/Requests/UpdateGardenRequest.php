<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateGardenRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya user yang merupakan pengelola kebun ini yang boleh mengupdate
        return Gate::allows('manage-garden', $this->route('garden'));
    }

    public function rules(): array
    {
        return [
            'nama_kebun' => 'sometimes|required|string|max:255',
            'alamat' => 'nullable|string',
        ];
    }
}
