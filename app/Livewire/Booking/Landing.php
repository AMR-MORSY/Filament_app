<?php

namespace App\Livewire\Booking;

use App\Models\Clinic;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.site-layout')]
class Landing extends Component
{
    public ?int $clinic_id = null;

    public string $when = 'soonest';

    public function search(): void
    {
        $this->redirect(route('search', array_filter([
            'clinic' => $this->clinic_id,
            'when' => $this->when !== 'soonest' ? $this->when : null,
        ])), navigate: true);
    }
//array_filter() is used to remove empty values from an array. It filters the array and returns a new array containing only the elements that pass a certain condition. By default, it removes elements that are considered "empty" in PHP, such as null, false, 0, and empty strings.
// $data = ['name' => 'Amr', 'email' => '', 'phone' => null, 'age' => 0];
// $result = array_filter($data);
// ['name' => 'Amr']  — empty string, null, 0, and false are all dropped

    public function render()
    {
        return view('livewire.booking.landing', [
            'clinics' => Clinic::query()
                ->where('is_active', true)
                ->withCount(['doctors' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('name')
                ->get(),
        ]);
    }
}
