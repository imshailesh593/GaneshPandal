<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Games are now created with just a name; date and time are optional.
        DB::statement('ALTER TABLE games MODIFY date DATE NULL, MODIFY time TIME NULL');
    }

    public function down(): void
    {
        DB::table('games')->whereNull('date')->update(['date' => now()->toDateString()]);
        DB::table('games')->whereNull('time')->update(['time' => '00:00:00']);

        DB::statement('ALTER TABLE games MODIFY date DATE NOT NULL, MODIFY time TIME NOT NULL');
    }
};
