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

    public function owner()
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }
    // =================================================================


    // Relasi lain yang sudah ada (untuk kelengkapan)
    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function plots()
    {
        return $this->hasMany(Plot::class);
    }

    public function journals()
    {
        return $this->hasMany(Journal::class);
    }
}