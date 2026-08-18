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
        Schema::create('schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_available')->default(false); // false = day off, true = special override
            $table->time('start_time')->nullable(); // only if is_available override with custom hours
            $table->time('end_time')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->unique(['doctor_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_exceptions');
    }
};
