<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fin_documents', function (Blueprint $table) {
            $table->dropForeign(['cert_a_officer_id']);
            $table->dropForeign(['cert_b_officer_id']);
        });

        Schema::table('fin_documents', function (Blueprint $table) {
            $table->string('cert_a_officer_id')->nullable()->change();
            $table->string('cert_b_officer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('fin_documents', function (Blueprint $table) {
            $table->unsignedBigInteger('cert_a_officer_id')->nullable()->change();
            $table->unsignedBigInteger('cert_b_officer_id')->nullable()->change();
        });

        Schema::table('fin_documents', function (Blueprint $table) {
            $table->foreign('cert_a_officer_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('cert_b_officer_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};