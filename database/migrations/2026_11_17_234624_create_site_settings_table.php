<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('tagline')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color', 9)->default('#FF5A36');
            $table->string('accent_color', 9)->default('#6EE7D8');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('og_image')->nullable();
            $table->string('footer_text', 500)->nullable();
            $table->string('contact_notification_email')->nullable();
            $table->boolean('show_contact_form')->default(true);
            $table->boolean('enable_3d')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
