<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNivelMenu extends Migration
{
    public function up()
    {
        Schema::table('menu', function (Blueprint $table) {
            $table->string('nivel',10)->after('icone')->nullable();
        });
    }
}
