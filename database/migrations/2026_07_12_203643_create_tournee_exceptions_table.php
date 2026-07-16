<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourneeExceptionsTable extends Migration
{
    public function up()
    {
        Schema::create('tournee_exceptions', function (Blueprint $table) {
            $table->id();

            // NULL = exception globale (tous les sites), sinon spécifique à un site
            $table->foreignId('site_id')->nullable()->constrained('sites')->onDelete('cascade');

            // Date de l'exception
            $table->date('date');

            // Label affiché ex: "Noël", "1er Mai", "Fermeture exceptionnelle"
            $table->string('label', 100);

            // true = fermé ce jour, false = ouvert exceptionnellement (jour normalement fermé)
            $table->boolean('is_closed')->default(true);

            // Créneaux personnalisés pour ce jour (si is_closed = false)
            // NULL = utiliser les créneaux normaux
            $table->json('creneaux_custom')->nullable();

            // Notes
            $table->string('notes', 200)->nullable();

            $table->timestamps();

            // Un site ne peut pas avoir deux exceptions le même jour
            $table->unique(['site_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('tournee_exceptions');
    }
}