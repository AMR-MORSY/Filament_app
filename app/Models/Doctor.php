<?php

namespace App\Models;

use App\Models\DoctorSchedule;
use App\Models\ScheduleException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Doctor extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
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

    public function appointments():HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ScheduleException::class);
    }

     public function registerMediaCollections(): void
    {
        $this->addMediaCollection('main_image')
        ->useDisk('doctors')
        ->singleFile()
        ->storeConversionsOnDisk('doctors')
        ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
        // ->maxFilesize(5 * 1024 * 1024) // 5MB limit
        ->useFallbackUrl('/images/doctors/doctor-placeholder.jpg')
        ->useFallbackPath(public_path('images/doctors/doctor-placeholder.jpg'));

    }

      public function registerMediaConversions(?Media $media = null): void
    {
        $this
        ->addMediaConversion('thumb')
        ->fit(Fit::Crop, 150, 150) // square avatar — fine for lists, cards, badges
        ->format('webp')
        ->quality(80)
        ->nonQueued();

    $this
        ->addMediaConversion('medium')
        ->fit(Fit::Crop, 300, 400) // portrait ratio — better for profile pages, booking flow doctor cards
        ->format('webp')
        ->quality(85)
        ->performOnCollections('main_image')
        ->nonQueued(); // keep consistent with thumb — see note below
    }
}
