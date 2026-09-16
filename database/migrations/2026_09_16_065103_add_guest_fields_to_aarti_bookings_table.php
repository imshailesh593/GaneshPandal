<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admins can book a slot on behalf of a guest or the mandal's chief,
        // who may not be a registered member.
        DB::statement('ALTER TABLE aarti_bookings MODIFY member_id BIGINT UNSIGNED NULL');

        Schema::table('aarti_bookings', function (Blueprint $table) {
            $table->string('guest_name')->nullable()->after('member_id');
        });
    }

    public function down(): void
    {
        Schema::table('aarti_bookings', function (Blueprint $table) {
            $table->dropColumn('guest_name');
        });

        DB::statement('ALTER TABLE aarti_bookings MODIFY member_id BIGINT UNSIGNED NOT NULL');
    }
};
