<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderLayananDB extends Model
{
    use HasFactory;

    protected $connection = 'mysql2';
    protected $table = 'erm_order_header';
    protected $primaryKey = 'idx';
}
