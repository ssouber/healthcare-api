<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinic_id')->constrained();
            $table->foreignId('doctor_id')->constrained();
            $table->foreignId('patient_id')->constrained();
            $table->string('status')->default('scheduled'); // 'scheduled' | 'cancelled'
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->timestamp('deleted_at'); // soft delete
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }
};
