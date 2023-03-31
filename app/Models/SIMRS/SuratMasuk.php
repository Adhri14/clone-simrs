<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;
    protected $connection = 'mysql5';
    protected $table = 'ts_surat_masuk';
    protected $primaryKey = 'id_surat_masuk';

    protected $guarded = ['id'];
}
