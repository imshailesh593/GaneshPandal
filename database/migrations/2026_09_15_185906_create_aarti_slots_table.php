<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aarti_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('festival_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('time')->default('20:30:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['festival_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aarti_slots');
    }
};
