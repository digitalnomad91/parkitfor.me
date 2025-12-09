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
        Schema::create('whois_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->onDelete('cascade');
            $table->text('raw_whois_data');
            $table->string('registrar')->nullable();
            $table->date('creation_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->date('updated_date')->nullable();
            $table->json('nameservers')->nullable();
            $table->string('status')->nullable();
            $table->string('registrant_name')->nullable();
            $table->string('registrant_email')->nullable();
            $table->string('registrant_organization')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whois_records');
    }
};
