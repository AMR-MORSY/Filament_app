<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'name' => 'Dr. ' . $this->faker->firstName() . ' ' . $this->faker->lastName(),
            'specialty' => $this->faker->randomElement([
                'Cardiologist', 'Dermatologist', 'Pediatrician', 'Orthopedic Surgeon',
                'Neurologist', 'Ophthalmologist', 'ENT Specialist', 'Radiologist',
                'General Surgeon', 'Internist', 'Gynecologist', 'Urologist',
                'Psychiatrist', 'Dentist', 'Physiotherapist',
            ]),
            'bio' => $this->faker->paragraph(3),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'photo' => null,
            'is_active' => $this->faker->boolean(90),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withPhoto(): static
    {
        return $this->state(fn (array $attributes) => [
            'photo' => $this->faker->imageUrl(),
        ]);
    }
    public function forClinic(Clinic $clinic): static
    {
        return $this->state(fn (array $attributes) => [
            'clinic_id' => $clinic->id,
        ]);
    }
}
