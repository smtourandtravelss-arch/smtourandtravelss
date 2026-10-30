<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('price_mode', 20)->nullable()->after('button_text');   // price | request | hide
            $table->string('usp_columns', 5)->nullable()->after('price_mode');    // '' = master, 1, 2
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['price_mode', 'usp_columns']);
        });
    }
};
