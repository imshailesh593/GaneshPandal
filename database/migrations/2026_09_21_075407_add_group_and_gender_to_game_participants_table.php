<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Results are now winners only (1st-3rd) per event, age group and gender.
        DB::table('game_participants')->where('position', 'participation')->delete();
        DB::statement("ALTER TABLE game_participants MODIFY position ENUM('first','second','third') NOT NULL");

        Schema::table('game_participants', function (Blueprint $table) {
            $table->enum('age_group', ['small', 'medium', 'large'])->after('member_id');
            $table->enum('gender', ['male', 'female'])->after('age_group');

            // At most one winner per rank in each event / group / gender.
            $table->unique(['game_id', 'age_group', 'gender', 'position'], 'game_winner_rank_unique');
        });
    }

    public function down(): void
    {
        Schema::table('game_participants', function (Blueprint $table) {
            $table->dropUnique('game_winner_rank_unique');
            $table->dropColumn(['age_group', 'gender']);
        });

        DB::statement("ALTER TABLE game_participants MODIFY position ENUM('first','second','third','participation') NOT NULL");
    }
};
