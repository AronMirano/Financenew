<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fin_documents', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->unique();
            $table->string('kind'); // 'obligation' (OBR/ORS) or 'utilization' (BUR/BURS)
            $table->date('date');
            $table->string('fund_cluster')->nullable();
            $table->string('payee_name')->nullable();
            $table->string('office')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('cert_a_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('cert_a_date')->nullable();
            $table->foreignId('cert_b_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('cert_b_date')->nullable();
            $table->string('status')->default('Draft'); // Draft/Certified/Obligated/Utilized/Paid - derived, but persisted
            $table->timestamps();

            $table->index('kind');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fin_documents');
    }
};
