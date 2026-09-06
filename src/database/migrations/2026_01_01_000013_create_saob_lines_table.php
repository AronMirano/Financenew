<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saob_lines', function (Blueprint $table) {
            $table->id();
            $table->string('sheet_key'); // '100','200','301','FHE','LFP', etc.
            $table->string('banner'); // 'current' or 'continuing'
            $table->string('label')->nullable(); // e.g. "Basic Salary - Civilian"
            $table->string('cls')->nullable(); // allotment class, e.g. "Personnel Services"
            $table->string('object_code')->nullable(); // UACS object code
            $table->decimal('authorized_appropriations', 15, 2)->default(0); // col 1
            $table->decimal('allotment_received', 15, 2)->default(0); // col 2
            $table->decimal('augmentations', 15, 2)->default(0); // col 3
            $table->decimal('modifications', 15, 2)->default(0); // col 4
            $table->decimal('obligations_incurred', 15, 2)->default(0); // col 8
            $table->decimal('unpaid_obligations', 15, 2)->default(0); // col 10
            $table->timestamps();

            $table->index(['sheet_key', 'banner']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saob_lines');
    }
};
