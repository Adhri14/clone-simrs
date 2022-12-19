<?php

namespace App\Models\RIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuanganRIS extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'ris_ruangan';
}
