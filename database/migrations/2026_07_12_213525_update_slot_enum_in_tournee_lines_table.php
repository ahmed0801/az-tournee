<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateSlotEnumInTourneeLinesTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE tournee_lines MODIFY COLUMN slot 
            ENUM('matin','apres_midi','9h-11h','11h-12h','13h-14h','15h-16h','17h-18h') 
            NOT NULL DEFAULT 'matin'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE tournee_lines MODIFY COLUMN slot 
            ENUM('matin','apres_midi') 
            NOT NULL DEFAULT 'matin'");
    }
}