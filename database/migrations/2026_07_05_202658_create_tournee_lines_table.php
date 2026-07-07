<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tournee_lines', function (Blueprint $table) {
            $table->id();

            // Source
            $table->unsignedBigInteger('site_id');
            $table->string('source_type');                    // facture_vente | commande_achat | bl
            $table->unsignedBigInteger('source_id');          // id de la facture/bl sur le site source
            $table->string('source_numdoc');                  // ex: FV25111721
            $table->unsignedBigInteger('source_line_id')->nullable();

            // Article
            $table->string('article_code');
            $table->string('article_name');
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('barcode')->nullable();            // code-barres connu

            // Fournisseur
            $table->unsignedBigInteger('fournisseur_id')->nullable();
            $table->string('fournisseur_name')->nullable();

            // Assignation
            $table->unsignedBigInteger('chauffeur_id')->nullable();
            $table->date('date_tournee');
            $table->enum('slot', ['matin', 'apres_midi']);

            // Statut
            $table->enum('statut', [
                'en_attente',
                'assigné',
                'en_route',
                'recupere',
                'au_magasin',
                'probleme',
            ])->default('en_attente');

            // Scan
            $table->string('scanned_barcode')->nullable();
            $table->timestamp('scanned_at')->nullable();
            $table->text('probleme_notes')->nullable();

            // Infos vendeur
            $table->text('notes')->nullable();
            $table->string('created_by_name')->nullable();

            $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
            $table->foreign('chauffeur_id')->references('id')->on('chauffeurs')->onDelete('set null');
            $table->foreign('fournisseur_id')->references('id')->on('fournisseurs')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournee_lines');
    }
};