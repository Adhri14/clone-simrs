<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananHeader extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'ts_layanan_header';

    public function layanan_details()
    {
        return $this->hasMany(LayananDetail::class, 'kode_layanan_header', 'kode_layanan_header');
    }
}
