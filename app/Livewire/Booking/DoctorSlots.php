<?php

namespace App\Livewire\Booking;

use App\Models\Doctor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The standalone form of step two, kept so /doctors/{doctor} stays a shareable
 * link. All the picking logic lives in SlotPicker, which the search page mounts
 * inline — this page is just the frame around it.
 */
#[Layout('components.site-layout')]
class DoctorSlots extends Component
{
    public Doctor $doctor;

    #[Url]
    public ?string $date = null;

    #[Url]
    public ?string $time = null;

    public ?int $duration = null;

    public function mount(Doctor $doctor): void
    {
        abort_unless($doctor->is_active, 404);

        $this->doctor = $doctor;
    }

    /** Keeps the step rail in step with the picker below it. */
    #[On('slot-selected')]
    public function onSlotSelected(?string $date = null, ?string $time = null, ?int $duration = null): void
    {
        $this->date = $date;
        $this->time = $time;
        $this->duration = $duration;
    }

    public function render()
    {
        return view('livewire.booking.doctor-slots');
    }
}
