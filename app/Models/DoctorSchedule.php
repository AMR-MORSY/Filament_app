<?php

namespace App\Models;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorSchedule extends Model
{
    use HasFactory;
        protected $fillable = [
        'doctor_id',
        'day_of_week',
        'start_time',
        'end_time',
        'slot_duration',
        'is_active',
    ];
    
    const DAYS = [
        0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday',
        4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday',
    ];
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? 'Unknown';
    }
}
