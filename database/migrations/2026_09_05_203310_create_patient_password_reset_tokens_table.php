<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Patients get their own reset-token table.
     *
     * Both brokers previously shared `password_reset_tokens`, so a token issued
     * for a patient would validate against a staff account with the same email.
     * Separate tables remove that overlap entirely.
     */
    public function up(): void
    {
        Schema::create('patient_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_password_reset_tokens');
    }
};
