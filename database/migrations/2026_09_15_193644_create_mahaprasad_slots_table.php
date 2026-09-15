<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahaprasad_slots', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->text('description');
            $table->foreignId('submitted_by')->nullable()->constrained('members')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            // Generated column + unique index: MySQL unique indexes allow multiple
            // NULLs, so this enforces "at most one approved sponsor per day"
            // while pending/rejected rows (NULL here) can repeat for the same date.
            $table->date('approved_date')
                ->nullable()
                ->storedAs("IF(status = 'approved', date, NULL)");
            $table->unique('approved_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahaprasad_slots');
    }
};
