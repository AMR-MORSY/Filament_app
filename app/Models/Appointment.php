<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{

    protected $fillable = [
        'clinic_id',
        'doctor_id',
        'patient_id',
        'guest_name',
        'guest_phone',
        'guest_email',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'admin_notes',
    ];
    protected $casts = [
        'appointment_date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
    ];
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
