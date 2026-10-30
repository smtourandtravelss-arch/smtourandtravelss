<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Small icon shown on the corner of a service card photo. */
    public function up(): void
    {
        if (Schema::hasColumn('services', 'icon')) {
            return;
        }
        Schema::table('services', function (Blueprint $table) {
            $table->string('icon', 40)->nullable()->after('image_alt');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
