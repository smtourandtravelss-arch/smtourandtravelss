<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('type', 20)->default('page')->after('id');          // page | post | landing
            $table->foreignId('category_id')->nullable()->after('type');
            $table->text('excerpt')->nullable();
            $table->string('featured_image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->longText('custom_schema')->nullable();                     // JSON-LD added by the admin
            $table->index('type');
        });

        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from_path')->unique();                             // /old-page
            $table->string('to_url', 500);                                     // /new-page or https://…
            $table->unsignedSmallInteger('code')->default(301);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hits')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['type', 'category_id', 'excerpt', 'featured_image', 'published_at', 'custom_schema']);
        });
        Schema::dropIfExists('categories');
    }
};
