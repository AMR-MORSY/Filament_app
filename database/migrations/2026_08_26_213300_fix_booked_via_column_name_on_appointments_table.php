<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The original migration created this column as `' booked_via'` (leading space typo),
     * so MySQL stores it under that literal name and every insert referencing the
     * correctly-spelled `booked_via` fails with "Unknown column".
     */
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE appointments CHANGE ` booked_via` `booked_via` ENUM('patient_self','staff','guest') NOT NULL DEFAULT 'guest'"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE appointments CHANGE `booked_via` ` booked_via` ENUM('patient_self','staff','guest') NOT NULL DEFAULT 'guest'"
        );
    }
};
