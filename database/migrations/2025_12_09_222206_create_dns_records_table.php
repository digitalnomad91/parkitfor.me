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
        Schema::create('dns_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->string('record_type', 10); // A, AAAA, MX, NS, CNAME, TXT, etc.
            $table->string('name'); // hostname/subdomain
            $table->text('value'); // IP address, hostname, or text value
            $table->integer('ttl')->nullable(); // Time to live
            $table->integer('priority')->nullable(); // For MX records
            $table->json('raw_data')->nullable(); // Store full DNS response
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['domain_id', 'record_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dns_records');
    }
};
