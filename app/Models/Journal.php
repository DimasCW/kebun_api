<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'garden_id',
        'plot_id',
        'penulis_id',
        'judul',
        'deskripsi',
        'foto_url',
        'alamat_lokasi',
        'koordinat_lat',
        'koordinat_lng',
    ];

    // Relasi: Jurnal ini ditulis oleh satu user
    public function author()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    // Relasi: Jurnal ini milik satu kebun
    public function garden()
    {
        return $this->belongsTo(Garden::class);
    }

    // Relasi: Jurnal ini milik satu petak
    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }
}