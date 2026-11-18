<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces the 3D toggle with a light/dark/system default theme
 * and updates the default brand colors to the indigo → teal palette.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('enable_3d');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('default_theme', 16)->default('light')->after('accent_color');
            $table->string('primary_color', 9)->default('#4F46E5')->change();
            $table->string('accent_color', 9)->default('#14B8A6')->change();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('default_theme');
            $table->boolean('enable_3d')->default(true);
            $table->string('primary_color', 9)->default('#FF5A36')->change();
            $table->string('accent_color', 9)->default('#6EE7D8')->change();
        });
    }
};
