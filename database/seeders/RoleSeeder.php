<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        // Shield's permissions are created by a CLI command, not by a migration,
        // so on a fresh database Permission::all() below would be empty and every
        // role would be seeded with nothing — a panel you can log into but not use.
        // Generate them here so `migrate:fresh --seed` yields a working system.
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'permissions',
            '--ignore-existing-policies' => true,
            '--silent' => true,
            '--no-interaction' => true,
        ]);

        // Spatie caches the permission registry; the rows just created must be
        // visible to the syncPermissions() calls below.
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Shield's config sets `define_via_gate => false`, so super_admin is a real
        // role holding every permission rather than a gate bypass. Nothing else
        // created it, which meant `migrate:fresh --seed` produced a database with
        // no one able to reach the admin panel.
        $superAdmin = Role::firstOrCreate([
            'name' => config('filament-shield.super_admin.name', 'super_admin'),
            'guard_name' => 'web',
        ]);
        $superAdmin->syncPermissions(Permission::all());

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
