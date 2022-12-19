<?php

namespace App\Models\RIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPemeriksaanRIS extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'ris_jenis_pemeriksaan';
}
