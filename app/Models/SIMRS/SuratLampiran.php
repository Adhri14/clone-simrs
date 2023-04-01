<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratLampiran extends Model
{
    use HasFactory;
    protected $connection = 'mysql5';
    protected $table = 'ts_surat_lampiran';
    protected $guarded = ['id'];



}
