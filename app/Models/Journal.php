<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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


    // ==========================================================
    // == BAGIAN PENTING YANG KEMUNGKINAN BESAR HILANG / SALAH ==
    // ==========================================================

    /**
     * Mendefinisikan relasi: Jurnal ini ditulis oleh satu User.
     * Nama fungsi ('author') harus cocok dengan yang ada di 'with()'.
     */
    public function author()
    {
        // 'penulis_id' adalah foreign key di tabel 'journals'
        return $this->belongsTo(User::class, 'penulis_id');
    }

    /**
     * Mendefinisikan relasi: Jurnal ini milik satu Plot.
     * Nama fungsi ('plot') harus cocok dengan yang ada di 'with()'.
     */
    public function plot()
    {
        return $this->belongsTo(Plot::class);
    }

    /**
     * Mendefinisikan relasi: Jurnal ini milik satu Garden.
     */
    public function garden()
    {
        return $this->belongsTo(Garden::class);
    }
}