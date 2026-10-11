<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = ['patient_id', 'date', 'time_slot', 'status'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
