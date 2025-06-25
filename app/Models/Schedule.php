<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'garden_id',
        'judul',
        'deskripsi',
        'tanggal_kegiatan',
        'penanggung_jawab_ids',
        'status',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'datetime',
        'penanggung_jawab_ids' => 'array', // Agar Laravel otomatis mengubah JSON menjadi Array PHP
    ];

    // Relasi: Jadwal ini milik satu kebun
    public function garden()
    {
        return $this->belongsTo(Garden::class);
    }
}