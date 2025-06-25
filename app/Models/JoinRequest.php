<?php
// app/Models/JoinRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'garden_id',
        'status',
    ];

    // Relasi: Permintaan ini dibuat oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Permintaan ini untuk satu kebun
    public function garden()
    {
        return $this->belongsTo(Garden::class);
    }
}