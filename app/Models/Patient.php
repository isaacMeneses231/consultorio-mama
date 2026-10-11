<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['name', 'phone', 'notes'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
