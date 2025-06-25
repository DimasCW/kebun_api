<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garden extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kebun',
        'alamat',
        'pemilik_id',
        'denah_url', // Bahkan jika null, lebih baik didaftarkan
    ];

    // ... sisa relasi ...
}