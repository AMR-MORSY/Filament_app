<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorSchedule>
 */
class DoctorScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'slot_duration' => $this->faker->randomElement([15, 20, 30]),
            'is_active' => true,
        ];
    }

    public function forDoctor(Doctor $doctor): static
    {
        return $this->state(fn (array $attributes) => [
            'doctor_id' => $doctor->id,
        ]);
    }
}
