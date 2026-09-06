<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utilization_rows', function (Blueprint $table) {
            $table->id();
            $table->string('particulars'); // e.g. "Affiliation Fee - Medicine"
            $table->string('section')->nullable();
            $table->decimal('balance_2022', 15, 2)->default(0);
            $table->decimal('receipts_fhe', 15, 2)->default(0);
            $table->decimal('collections_2023', 15, 2)->default(0);
            $table->decimal('pre', 15, 2)->default(0); // the authorized PRE ceiling
            $table->decimal('carry_over_2022', 15, 2)->default(0);
            $table->decimal('util_ps', 15, 2)->default(0);
            $table->decimal('util_mode', 15, 2)->default(0); // verbatim from source: MODE not MOOE
            $table->decimal('util_co', 15, 2)->default(0);
            $table->decimal('disb_ps', 15, 2)->default(0);
            $table->decimal('disb_mode', 15, 2)->default(0);
            $table->decimal('disb_co', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utilization_rows');
    }
};
