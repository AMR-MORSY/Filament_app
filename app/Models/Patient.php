<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Patient extends  Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
