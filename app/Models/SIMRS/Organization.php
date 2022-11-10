<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_of_id',
        'satusehat_uuid',
        'identifier_id',
        // telecom
        'phone',
        'email',
        'url',
        // address
        'province_id',
        'city_id',
        'district_id',
        'village_id',
        'city',
        'line',
        'postalCode',
        // resource
        'active',
        'name',
    ];
}
