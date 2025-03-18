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
        Schema::create('contacts_details', function (Blueprint $table) {
            $table->id();
            $table->string('cb_cognome');
            $table->string('cb_codicefiscale')->unique();
            $table->date('cb_datadinascita');
            $table->string('cb_luogodinascita');      
            $table->string('cb_provinciadinascita');
            $table->string('cb_indirizzodiresidenza');
            $table->string('cb_provdiresidenza')->nullable();
            $table->string('cb_cap')->nullable();
            $table->string('cb_telefono');
            $table->string('cb_nome');
            $table->string('cb_citta');
            $table->string('cb_professionedisciplina')->nullable();
            $table->string('cb_ordine')->nullable();
            $table->string('cb_numeroiscrizione')->nullable();
            $table->string('cb_reclutamento')->nullable();
            $table->string('cb_codicereclutamento')->nullable();
            $table->string('cb_professione')->nullable();
            $table->string('cb_profiloprofessionale');
            $table->string('cb_settore')->nullable();
            $table->string('cb_societa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts_details');
    }
};