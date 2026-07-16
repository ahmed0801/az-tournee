<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourneeParametresTable extends Migration
{
    public function up()
    {
        Schema::create('tournee_parametres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');

            // Jours actifs — JSON array ex: ["lundi","mardi","mercredi","jeudi","vendredi","samedi"]
            $table->json('jours_actifs')->default('["lundi","mardi","mercredi","jeudi","vendredi","samedi"]');

            // Heure d'ouverture et fermeture
            $table->time('heure_debut')->default('08:00');
            $table->time('heure_fin')->default('18:00');

            // Créneaux disponibles — JSON array de objets
            // ex: [{"label":"9h-11h","debut":"09:00","fin":"11:00"},...]
            $table->json('creneaux')->default('[
                {"label":"9h-11h","debut":"09:00","fin":"11:00"},
                {"label":"11h-12h","debut":"11:00","fin":"12:00"},
                {"label":"13h-14h","debut":"13:00","fin":"14:00"},
                {"label":"15h-16h","debut":"15:00","fin":"16:00"},
                {"label":"17h-18h","debut":"17:00","fin":"18:00"}
            ]');

            // Délai minimum avant la tournée (en heures)
            // Ex: 2 = on ne peut pas ajouter une pièce moins de 2h avant le créneau
            $table->integer('delai_min_heures')->default(1);

            // Actif ou non
            $table->boolean('is_active')->default(true);

            // Notes internes
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique('site_id'); // 1 paramètre par site
        });
    }

    public function down()
    {
        Schema::dropIfExists('tournee_parametres');
    }
}