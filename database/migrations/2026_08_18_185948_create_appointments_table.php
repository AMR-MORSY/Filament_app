<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();

            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();

            $table->foreignId('created_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_email')->nullable();

            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'no_show'])
                ->default('pending');
            $table->enum('booked_via', ['patient_self', 'staff', 'guest'])->default('guest');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'appointment_date']);

            // Prevents two appointments for the same doctor at the exact same start time
            $table->unique(['doctor_id', 'appointment_date', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
