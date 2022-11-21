<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuTamu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'organisasi',
        'phone',
        'alamat',
        'tujuan',
    ];
}
