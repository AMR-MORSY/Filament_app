<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Staff accounts and the role each one needs to reach a panel.
     *
     * User::canAccessPanel() gates the admin panel on super_admin|clinic-manager
     * and the staff panel on those plus staff-manager|staff, so an account with
     * no role can authenticate but reaches neither — which surfaces as the same
     * "credentials do not match" message as a wrong password.
     */
    public function run(): void
    {
        // Local convenience only. Never commit a real password: set
        // SEED_USER_PASSWORD in .env for anything shared.
        $password = env('SEED_USER_PASSWORD', '@Mobinil2020');

        $accounts = [
            ['Amr Morsy',      'morsy.mamr@gmail.com',     'super_admin'],
            ['Clinic Manager', 'clinic.manager@gmail.com', 'clinic-manager'],
            ['Staff Manager',  'amr.mmorsy@orange.com',    'staff-manager'],
            ['Staff',          'staff.member@gmail.com',   'staff'],
        ];

        foreach ($accounts as [$name, $email, $role]) {
            // firstOrCreate, so re-seeding never resets a password someone changed.
            $user = User::firstOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make($password), 'is_active' => true],
            );

            // Create the role if it is missing rather than throwing. Seeder order
            // should not decide whether anyone can log in.
            if ($role === null) {
                continue;
            }

            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);

            $user->syncRoles([$role]);
        }
    }
}
