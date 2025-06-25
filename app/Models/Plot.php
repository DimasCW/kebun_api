<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plot extends Model
{
    use HasFactory;

    protected $fillable = [
        'garden_id',
        'nama_petak',
        'pemilik_id',
        'posisi_peta_x',
        'posisi_peta_y',
        'status',
    ];

    // Relasi: Petak ini milik satu kebun
    public function garden()
    {
        return $this->belongsTo(Garden::class);
    }

    // Relasi: Petak ini bisa dimiliki oleh satu user (anggota)
    public function owner()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }
}