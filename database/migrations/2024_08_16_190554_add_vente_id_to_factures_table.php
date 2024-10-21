<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVenteIdToFacturesTable extends Migration
{
    public function up()
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->unsignedBigInteger('vente_id')->after('id');
            $table->foreign('vente_id')->references('id')->on('ventes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropForeign(['vente_id']);
            $table->dropColumn('vente_id');
        });
    }
}
