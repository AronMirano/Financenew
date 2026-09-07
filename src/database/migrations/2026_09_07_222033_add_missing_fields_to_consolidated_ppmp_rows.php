<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consolidated_ppmp_rows', function (Blueprint $table) {
            $table->string('obr')->nullable()->after('request');
            $table->string('payee')->nullable()->after('obr');
            $table->string('description')->nullable()->after('payee');
            $table->decimal('amount', 15, 2)->default(0)->after('description');
            $table->decimal('balance', 15, 2)->default(0)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consolidated_ppmp_rows', function (Blueprint $table) {
            $table->dropColumn(['obr', 'payee', 'description', 'amount', 'balance']);
        });
    }
};
