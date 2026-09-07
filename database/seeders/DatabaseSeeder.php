<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $clinics = collect([
            ['name' => 'Cardiology', 'floor' => '2nd Floor, Wing B'],
            ['name' => 'Dermatology', 'floor' => '1st Floor'],
            ['name' => 'Pediatrics', 'floor' => '3rd Floor'],
            ['name' => 'Orthopedics', 'floor' => '2nd Floor, Wing A'],
        ])->map(fn (array $attrs) => Clinic::create($attrs + ['is_active' => true]));

        $doctorNames = [
            'Cardiology' => [
                ['name' => 'Dr. A. Hassan', 'specialty' => 'Interventional cardiology'],
                ['name' => 'Dr. M. Farouk', 'specialty' => 'Echocardiography'],
            ],
            'Dermatology' => [
                ['name' => 'Dr. S. Nabil', 'specialty' => 'Cosmetic dermatology'],
            ],
            'Pediatrics' => [
                ['name' => 'Dr. L. Youssef', 'specialty' => 'General pediatrics'],
            ],
            'Orthopedics' => [
                ['name' => 'Dr. K. Adly', 'specialty' => 'Sports medicine'],
            ],
        ];

        $phone = 1000;

        foreach ($clinics as $clinic) {
            foreach ($doctorNames[$clinic->name] as $attrs) {
                $doctor = Doctor::create($attrs + [
                    'clinic_id' => $clinic->id,
                    'phone' => '01' . str_pad((string) $phone++, 9, '0', STR_PAD_LEFT),
                    'is_active' => true,
                ]);

                foreach (range(0, 5) as $day) { // Sunday–Friday
                    $doctor->schedules()->create([
                        'day_of_week' => $day,
                        'start_time' => '09:00',
                        'end_time' => $day === 5 ? '13:00' : '17:00',
                        'slot_duration' => 30,
                        'is_active' => true,
                    ]);
                }
            }
        }

        $patient = Patient::factory()->create([
            'name' => 'Demo Patient',
            'email' => 'patient@example.com',
            'phone' => '01055500000',
            'password' => Hash::make('password'),
        ]);

        $cardiologist = Doctor::where('name', 'Dr. A. Hassan')->first();

        Appointment::create([
            'clinic_id' => $cardiologist->clinic_id,
            'doctor_id' => $cardiologist->id,
            'patient_id' => $patient->id,
            'appointment_date' => now()->addDays(2)->toDateString(),
            'start_time' => '09:30',
            'end_time' => '10:00',
            'status' => 'confirmed',
            'booked_via' => 'patient_self',
        ]);

        Appointment::create([
            'clinic_id' => $cardiologist->clinic_id,
            'doctor_id' => $cardiologist->id,
            'patient_id' => $patient->id,
            'appointment_date' => now()->subDays(10)->toDateString(),
            'start_time' => '11:00',
            'end_time' => '11:30',
            'status' => 'completed',
            'booked_via' => 'patient_self',
        ]);

        $this->call([
            RoleSeeder::class, 
            UserSeeder::class]);
    }
}
