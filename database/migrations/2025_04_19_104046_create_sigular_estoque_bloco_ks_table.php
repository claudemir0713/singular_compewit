<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSigularEstoqueBlocoKsTable extends Migration
{
    public function up()
    {
        schema::create('sigular_estoque_bloco_k', function (blueprint $table) {
            $table->id();
            $table->date('data')->nullable();
            $table->integer('prd_codigo')->index();
            $table->double('qtd')->nullable();
            $table->double('valor')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        schema::dropifexists('sigular_estoque_bloco_k');
    }
}
