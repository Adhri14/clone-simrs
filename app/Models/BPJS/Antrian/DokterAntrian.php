<?php

namespace App\Models\BPJS\Antrian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokterAntrian extends Model
{
    use HasFactory;
    protected $fillable = [
        'kodeDokter',
        'namaDokter',
    ];
}
