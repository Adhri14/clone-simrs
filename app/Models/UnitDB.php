<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitDB extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'mt_unit';
    protected $primaryKey = 'kode_unit';
    public $incrementing = false;
    protected $keyType = 'string';
}
