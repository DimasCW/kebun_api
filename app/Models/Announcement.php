<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'garden_id',
        'penulis_id',
        'judul',
        'isi',
    ];

    // Relasi: Pengumuman ini milik satu kebun
    public function garden()
    {
        return $this->belongsTo(Garden::class);
    }

    // Relasi: Pengumuman ini ditulis oleh satu user
    public function author()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }
}