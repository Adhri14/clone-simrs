<?php

namespace App\Models\SIMRS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encounter extends Model
{
    use HasFactory;

    protected $fillable = [
        // identifier
        'satusehat_uuid', #satusehat uuid
        'identifier_id',
        // location
        'location_id',
        'location_name',
        // participant
        'practitioner_id',
        'practitioner_name',
        // patient
        'patient_id',
        'patient_name',
        // periode
        'period_start',
        'period_end',
        // service provider / rumahsakit
        'provider_id',
        'status',
    ];
}
