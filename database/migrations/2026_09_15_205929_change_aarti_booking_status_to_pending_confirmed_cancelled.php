<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aarti_bookings', function (Blueprint $table) {
            $table->dropUnique(['active_slot_id']);
            $table->dropColumn('active_slot_id');
        });

        // Widen the enum first so existing 'booked' rows stay valid while we migrate them.
        DB::statement("ALTER TABLE aarti_bookings MODIFY status ENUM('booked','pending','confirmed','cancelled') NOT NULL DEFAULT 'pending'");
        DB::table('aarti_bookings')->where('status', 'booked')->update(['status' => 'confirmed']);
        DB::statement("ALTER TABLE aarti_bookings MODIFY status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('aarti_bookings', function (Blueprint $table) {
            // A slot is held (unavailable to others) while pending admin confirmation
            // or once confirmed; only 'cancelled' frees it up.
            $table->unsignedBigInteger('active_slot_id')
                ->nullable()
                ->storedAs("IF(status = 'cancelled', NULL, aarti_slot_id)");
            $table->unique('active_slot_id');
        });
    }

    public function down(): void
    {
        Schema::table('aarti_bookings', function (Blueprint $table) {
            $table->dropUnique(['active_slot_id']);
            $table->dropColumn('active_slot_id');
        });

        DB::statement("ALTER TABLE aarti_bookings MODIFY status ENUM('pending','confirmed','booked','cancelled') NOT NULL DEFAULT 'booked'");
        DB::table('aarti_bookings')->whereIn('status', ['pending', 'confirmed'])->update(['status' => 'booked']);
        DB::statement("ALTER TABLE aarti_bookings MODIFY status ENUM('booked','cancelled') NOT NULL DEFAULT 'booked'");

        Schema::table('aarti_bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('active_slot_id')
                ->nullable()
                ->storedAs("IF(status = 'booked', aarti_slot_id, NULL)");
            $table->unique('active_slot_id');
        });
    }
};
