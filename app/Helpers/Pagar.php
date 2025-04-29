<?php
namespace App\Helpers;

use App\Models\FIN_CONTAS;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Pagar {
    public static function pagar($dataI,$dataF,$forencedor,$tipo) {
        $filtros = [];
        $forencedor    = " AND CLIENTE LIKE '$forencedor%'";
        if($dataI){
            $filtros[]=['CON_DT_VENCIMENTO','>=',$dataI];
        };
        if($dataF){
            $filtros[]=['CON_DT_VENCIMENTO','<=',$dataF];
        };
        if($forencedor){
            $filtros[]=['PARTICIPANTE.PART_NOME','like','%'.$forencedor.'%'];
        };

        if($tipo){
            $filtros[]=['RIGHT(TRIM(CON_NUMERO),2)','in','('.$tipo.')'];

        };
        // dd($filtros);
        // $pagar = FIN_CONTAS::leftJoin('PARTICIPANTE','PARTICIPANTE.PART_CLIENTE_CODIGO','FIN_CONTAS.ENT_CODIGO')
        //                 ->where($filtros)
        //                 ->where('CON_TIPO',0)
        //                 ->whereIn('CON_SITUACAO',[0,1])
        //                 ->get([

        //                 ]);
        // return $pagar;
    }


}
