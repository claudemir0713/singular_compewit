<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProdutoMysqlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produto_mysql', function (Blueprint $table) {
            $table->integer('prd_codigo')->index();
            $table->string('prd_descricao',120)->nullable();
            $table->string('um_codigo',2)->nullable();
            $table->string('prd_tipo_produto',2)->nullable();
            $table->string('ncm_codigo',20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produto_mysqls');
    }
}
