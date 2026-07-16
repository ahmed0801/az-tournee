<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlotModifiedToTourneeLinesTable extends Migration
{
    public function up()
    {
        Schema::table('tournee_lines', function (Blueprint $table) {
            $table->boolean('slot_modified')->default(false)->after('slot');
        });
    }

    public function down()
    {
        Schema::table('tournee_lines', function (Blueprint $table) {
            $table->dropColumn('slot_modified');
        });
    }
}