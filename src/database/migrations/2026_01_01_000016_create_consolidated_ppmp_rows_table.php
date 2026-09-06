<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consolidated_ppmp_rows', function (Blueprint $table) {
            $table->id();
            $table->string('fund_type'); // 'fidu' or 'nonfidu'
            $table->string('ppmp')->nullable(); // PPMP line item number
            $table->string('unit')->nullable(); // requesting office/college
            $table->string('item')->nullable();
            $table->decimal('ps', 15, 2)->default(0);
            $table->decimal('mooe', 15, 2)->default(0); // verbatim from source: MOOE (not MODE) here
            $table->decimal('co', 15, 2)->default(0);
            $table->decimal('contingency', 15, 2)->default(0);
            $table->decimal('realignment', 15, 2)->default(0);
            $table->string('request')->nullable(); // PR# (non-fidu) or Request# (fidu)
            $table->timestamps();

            $table->index('fund_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidated_ppmp_rows');
    }
};
