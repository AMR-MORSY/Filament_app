<?php

namespace App\Models;

use App\Models\Appointment;
use App\Models\Doctor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinic extends Model
{
 
    use HasFactory;

    protected $fillable = ['name', 'floor', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

   
    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
