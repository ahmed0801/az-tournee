<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiteIdToChauffeursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chauffeurs', function (Blueprint $table) {
            $table->foreignId('site_id')->nullable()->after('id')
                  ->constrained('sites')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('chauffeurs', function (Blueprint $table) {
            $table->dropForeign(['site_id']);
            $table->dropColumn('site_id');
        });
    }
}

