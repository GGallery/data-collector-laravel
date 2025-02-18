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
        Schema::create('contacts_extra', function (Blueprint $table) {
            $table->id();
            $table->string('cb_cognome');
            $table->string('cb_codicefiscale')->unique();
            $table->date('cb_datadinascita');
            $table->string('cb_luogodinascita');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts_extra');
    }
};
