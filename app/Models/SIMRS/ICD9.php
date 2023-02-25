<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICD9 extends Model
{
    use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'mt_icd9';
    public $timestamps = false;
}
