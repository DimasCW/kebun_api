<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'garden_id',
        'role',
    ];

    /**
     * Mendefinisikan relasi: Keanggotaan ini milik satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendefinisikan relasi: Keanggotaan ini untuk satu Kebun.
     * INI KEMUNGKINAN BESAR YANG HILANG.
     */
    public function garden()
    {
        return $this->belongsTo(Garden::class);
    }
}