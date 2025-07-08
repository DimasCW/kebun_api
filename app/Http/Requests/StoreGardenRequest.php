<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGardenRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Siapapun yang sudah login boleh mencoba membuat kebun
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_kebun' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ];
    }
}