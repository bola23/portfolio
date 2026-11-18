<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500);
            $table->string('type', 32)->default('web_app');
            $table->string('region')->nullable();
            $table->string('role')->nullable();
            $table->longText('overview')->nullable();
            $table->longText('problem')->nullable();
            $table->longText('solution')->nullable();
            $table->longText('responsibilities')->nullable();
            $table->longText('architecture')->nullable();
            $table->json('features')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('live_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'sort_order']);
            $table->index(['is_featured', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
