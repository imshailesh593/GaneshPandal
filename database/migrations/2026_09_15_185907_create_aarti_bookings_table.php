<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aarti_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            // RESTRICT (not cascade): MySQL can't cascade-delete a column that a
            // stored generated column (active_slot_id, below) depends on.
            $table->foreignId('aarti_slot_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['booked', 'cancelled'])->default('booked');
            $table->timestamps();

            // Generated column + unique index: MySQL unique indexes allow multiple
            // NULLs, so this enforces "at most one booked row per slot" while
            // cancelled rows (NULL here) can repeat freely for the same slot.
            $table->unsignedBigInteger('active_slot_id')
                ->nullable()
                ->storedAs("IF(status = 'booked', aarti_slot_id, NULL)");
            $table->unique('active_slot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aarti_bookings');
    }
};
