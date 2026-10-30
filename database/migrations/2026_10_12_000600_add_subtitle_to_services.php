<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Small line under the card title, e.g. "Swift Dzire · Toyota Etios or similar". */
    public function up(): void
    {
        if (Schema::hasColumn('services', 'subtitle')) {
            return;
        }
        Schema::table('services', function (Blueprint $table) {
            $table->string('subtitle', 160)->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('subtitle');
        });
    }
};
