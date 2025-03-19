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
        Schema::create('sync_pointers', function (Blueprint $table) {
            $table->id();
            $table->string('platform_prefix', 32);
            $table->unsignedBigInteger('last_id_processed')->default(0);
            $table->timestamp('last_sync_date')->nullable();
            $table->timestamps();

            // Chiave esterna verso api_tokens_prefixes
            $table->foreign('platform_prefix')
                  ->references('prefix_token')
                  ->on('api_tokens_prefixes')
                  ->onDelete('cascade');
                  
            // Indice per ottimizzare le query
            $table->index('platform_prefix');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_pointers');
    }
};
