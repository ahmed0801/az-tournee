<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddLivreClientToTourneeLinesTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE tournee_lines MODIFY COLUMN statut 
            ENUM('en_attente','assigné','en_route','recupere','au_magasin','probleme','livre_client') 
            NOT NULL DEFAULT 'en_attente'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE tournee_lines MODIFY COLUMN statut 
            ENUM('en_attente','assigné','en_route','recupere','au_magasin','probleme') 
            NOT NULL DEFAULT 'en_attente'");
    }
}