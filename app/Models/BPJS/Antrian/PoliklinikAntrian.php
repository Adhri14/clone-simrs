<?php

namespace App\Models\BPJS\Antrian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliklinikAntrian extends Model
{
    use HasFactory;

    protected $fillable = [
        'kodePoli',
        'namaPoli',
        'kodeSubspesialis',
        'namaSubspesialis',
        'status',
    ];
}
