<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_paper_rows', function (Blueprint $table) {
            $table->id();
            $table->string('month'); // e.g. "Jan."
            $table->date('obr_date')->nullable();
            $table->string('obr_no')->nullable();
            $table->string('payee')->nullable();
            $table->string('particulars')->nullable();
            $table->string('mfo')->nullable();
            $table->string('rc_code')->nullable();
            $table->string('object_code')->nullable();
            $table->decimal('amount_current', 15, 2)->default(0);
            $table->decimal('amount_continuing', 15, 2)->default(0);
            $table->decimal('payment', 15, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->string('dv_no')->nullable();
            $table->string('check_no')->nullable();
            $table->timestamps();

            $table->index('month');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_paper_rows');
    }
};
