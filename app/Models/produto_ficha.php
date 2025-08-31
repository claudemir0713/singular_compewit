<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produto_ficha extends Model
{
    use HasFactory;
    protected $fillable= [
        'id'
        , 'cod'
        , 'descr'
        , 'grupo'
        , 'cod_pai'
        , 'desc_pai'
        , 'cod_comp'
        , 'desc_comp'
        , 'tipo_comp'
        , 'grupo_comp'
        , 'qtde_calc'
        , 'nivel'
    ];
    protected $primaryKey = 'id';
    protected $table = 'produto_ficha_mysql';
}
