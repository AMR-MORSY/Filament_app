<?php

namespace App\Models;

use App\Models\DoctorSchedule;
use App\Models\ScheduleException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = ['clinic_id', 'name', 'specialty', 'bio', 'email', 'phone', 'photo', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ScheduleException::class);
    }
}
