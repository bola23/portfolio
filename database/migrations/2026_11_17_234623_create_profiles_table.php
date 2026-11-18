<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('headline');
            $table->string('specialization')->nullable();
            $table->string('short_intro', 600)->nullable();
            $table->longText('bio')->nullable();
            $table->longText('approach')->nullable();
            $table->json('expertise_areas')->nullable();
            $table->unsignedTinyInteger('years_of_experience')->default(0);
            $table->string('availability_status', 32)->default('open');
            $table->string('availability_note')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->boolean('show_phone')->default(false);
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
