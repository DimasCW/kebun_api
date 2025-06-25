<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreJournalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User|null $user */ // <-- PETUNJUK UNTUK EDITOR
        $user = Auth::user();

        // Cek jika karena suatu hal user tidak terautentikasi, langsung tolak.
        if (!$user) {
            return false;
        }

        // Garis merah akan hilang dari baris di bawah ini.
        // Logikanya: Izinkan request HANYA JIKA user yang login adalah anggota dari kebun yang dituju.
        return $user->memberships()->where('garden_id', $this->garden_id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'garden_id' => 'required|exists:gardens,id',
            'plot_id' => 'required|exists:plots,id',
        ];
    }
}