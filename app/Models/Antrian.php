<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Antrian extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'jkn_antrian';

    protected $guarded = [
        "id",
    ];

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'nik', 'nik');
    }
}
