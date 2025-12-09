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
        Schema::create('scrape_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrape_id')->constrained()->onDelete('cascade');
            $table->string('url', 2048);
            $table->string('anchor_text')->nullable();
            $table->string('rel')->nullable();
            $table->string('target')->nullable();
            $table->enum('link_type', ['internal', 'external', 'mailto', 'tel', 'javascript', 'other'])->default('other');
            $table->boolean('is_nofollow')->default(false);
            $table->integer('position')->nullable(); // Order in which link appears on page
            $table->timestamps();
            
            // Indexes
            $table->index(['scrape_id', 'link_type']);
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrape_links');
    }
};
