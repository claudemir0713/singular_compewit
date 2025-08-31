<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProdutoFicha extends Migration
{
    public function up()
    {
        Schema::create('produto_ficha_mysql', function (Blueprint $table) {
            $table->id();
            $table->integer('cod')->nullable();
            $table->string('descr',120)->nullable();
            $table->integer('grupo')->nullable();
            $table->integer('cod_pai')->nullable();
            $table->string('desc_pai',120)->nullable();
            $table->integer('cod_comp')->nullable();
            $table->string('desc_comp',120)->nullable();
            $table->string('tipo_comp',2)->nullable();
            $table->integer('grupo_comp')->nullable();
            $table->double('qtde_calc')->nullable();
            $table->integer('nivel')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produto_ficha_mysql');
    }
}
