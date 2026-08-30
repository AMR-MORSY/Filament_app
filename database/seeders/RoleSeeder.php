<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinicManager = Role::firstOrCreate(['name' => 'clinic-manager', 'guard_name' => 'web']);
        $clinicManager->syncPermissions(
            Permission::where('name', 'like', '%:Clinic')
                ->orWhere('name', 'like', '%:Doctor')
                ->get()
        );
        // Note: DoctorSchedule has no standalone Filament resource (it's a
        // RelationManager under DoctorResource), so Shield never generates
        // "...:DoctorSchedule" permissions — schedule access rides on DoctorPolicy.

        $staffManager = Role::firstOrCreate(['name' => 'staff-manager', 'guard_name' => 'web']);
        $staffManager->syncPermissions(
            Permission::where('name', 'like', '%:User')->get()
        );
        // Note: no UserResource exists yet, so this syncs an empty set until one
        // is created and `shield:generate` is re-run against it.
        // Deliberately NOT syncing "%:Role" here — that would let staff-manager
        // edit roles/permissions themselves, a privilege-escalation risk. Keep
        // Role management on super_admin only.

        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions(
            Permission::where('name', 'like', '%:Appointment')
                ->orWhere('name', 'like', '%:Patient')
                ->get()
        );
    }
}
