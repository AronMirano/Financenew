<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fin_document_id')->constrained('fin_documents')->cascadeOnDelete();
            $table->string('kind'); // 'obligation' (a) | 'payable' (b) | 'payment' (c)
            $table->date('entry_date');
            $table->string('reference_no')->nullable(); // DV#, Check#/ADA/TRA, RCI/RADAI/RTRAI
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();

            $table->index('kind');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
