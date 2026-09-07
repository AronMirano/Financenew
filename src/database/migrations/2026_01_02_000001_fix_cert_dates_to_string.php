<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fin_documents', function (Blueprint $table) {
            $table->string('cert_a_date')->nullable()->change();
            $table->string('cert_b_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('fin_documents', function (Blueprint $table) {
            $table->date('cert_a_date')->nullable()->change();
            $table->date('cert_b_date')->nullable()->change();
        });
    }
};