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
        Schema::create('scrape_assets', function (Blueprint $table) {
            $table->id();
            $table->string('url', 2048)->unique();
            $table->string('type', 50); // css, js, image, font, video, audio, etc.
            $table->string('file_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('hash', 64)->nullable(); // SHA256 hash for deduplication
            $table->integer('download_attempts')->default(0);
            $table->enum('status', ['pending', 'downloaded', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('hash');
            $table->index('type');
            $table->index('status');
        });
        
        // Pivot table for scrapes and assets (many-to-many)
        Schema::create('scrape_asset_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scrape_id')->constrained()->onDelete('cascade');
            $table->foreignId('scrape_asset_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['scrape_id', 'scrape_asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrape_asset_pivot');
        Schema::dropIfExists('scrape_assets');
    }
};
