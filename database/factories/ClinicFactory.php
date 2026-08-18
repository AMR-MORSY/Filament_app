<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clinic>
 */
class ClinicFactory extends Factory
{
    protected $model = Clinic::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            'Cardiology', 'Dermatology', 'Pediatrics', 'Orthopedics',
            'Neurology', 'Ophthalmology', 'ENT', 'Radiology',
            'General Surgery', 'Internal Medicine', 'Gynecology', 'Urology',
            'Psychiatry', 'Dentistry', 'Physiotherapy',
        ];

        return [
            'name' => $this->faker->unique()->randomElement($departments),
            'floor' => $this->faker->randomElement([
                'Ground Floor', '1st Floor', '2nd Floor, Wing A',
                '2nd Floor, Wing B', '3rd Floor',
            ]),
            'description' => $this->faker->sentence(10),
            'is_active' => $this->faker->boolean(90), // mostly active
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
