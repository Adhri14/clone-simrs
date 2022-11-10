<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $fillable = [
        // identifier
        'ihs_id',
        'nik_id',
        'rm_id',
        'bpjs_id',
        // name
        'name',
        // telecom
        'phone',
        'email',
        // address
        'country',
        'city',
        'province_id',
        'city_id',
        'district_id',
        'village',
        'line',
        'postalCode',
        // photo
        'photo_id',
        // resource
        'active',
        'gender',
        'birthDate',
        'deceasedBoolean', #kematian
        'deceasedDateTime', #tanggal kematian
        'maritalStatus', #status menikah
        'communication', #bahasa
    ];
}
