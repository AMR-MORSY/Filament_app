<?php

namespace App\Models;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleException extends Model
{
    use HasFactory;

    protected $fillable = ['doctor_id', 'date', 'is_available', 'start_time', 'end_time', 'reason'];
    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
