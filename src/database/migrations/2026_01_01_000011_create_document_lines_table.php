<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fin_document_id')->constrained('fin_documents')->cascadeOnDelete();
            $table->string('rc_acronym')->nullable();
            $table->string('rc_code')->nullable();
            $table->string('object_code')->nullable(); // UACS sub-object code
            $table->string('particulars')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_lines');
    }
};
