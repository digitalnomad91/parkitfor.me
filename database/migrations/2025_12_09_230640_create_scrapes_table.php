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
        Schema::create('scrapes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->string('url', 2048);
            $table->string('title')->nullable();
            $table->longText('html_body')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->string('video_path')->nullable();
            $table->string('favicon_path')->nullable();
            
            // HTTP Request Details
            $table->integer('http_status_code')->nullable();
            $table->text('http_headers')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->string('content_type')->nullable();
            $table->bigInteger('content_length')->nullable();
            
            // Meta Information
            $table->text('meta_description')->nullable();
            $table->json('meta_tags')->nullable();
            $table->json('open_graph_data')->nullable();
            
            // Scraping Status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('scraped_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['domain_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrapes');
    }
};
