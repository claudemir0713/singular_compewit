<?php
namespace App\Helpers;

use App\Models\FIN_CONTAS;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Receber {
    public static function receber($dataI,$dataF,$cliente,$vendedor,$tipo,$status,$nf,$dtIEmissa,$dtFEmissa) {
        if($dataI){
            $dataI = " AND CON_DT_VENCIMENTO >= '$dataI'";
        }else{
            $dataI ='';
        };
        if($dataF){
            $dataF = " AND CON_DT_VENCIMENTO <= '$dataF'";
        }else{
            $dataF ='';
        }

        if($dtIEmissa){
            $dtIEmissa = " AND CON_DT_INCLUSAO >= '$dtIEmissa'";
        }else{
            $dtIEmissa ='';
        };
        if($dtFEmissa){
            $dtFEmissa = " AND CON_DT_INCLUSAO <= '$dtFEmissa'";
        }else{
            $dtFEmissa ='';
        }

        if($cliente){
            $cliente = " AND CLI.PART_NOME like '%$cliente%'";
        }else{
            $cliente ='';
        }
        if($vendedor){
            $vendedor = " AND REP.PART_NOME like '%$vendedor%'";
        }else{
            $vendedor ='';
        }
        if($tipo){
            if($tipo=='PD'){
                $tipo = " AND RIGHT(TRIM(CON_NUMERO),2) IN ('PD','CH')";
            }elseif($tipo!='PD'){
                $tipo = " AND RIGHT(TRIM(CON_NUMERO),2) NOT IN ('PD','CH')";
            }
        }else{
            $tipo = '';
        }
        if($status){
            $status = " AND FIN_CONTAS.CON_SITUACAO in ($status)";
        }else{
            $status = " AND FIN_CONTAS.CON_SITUACAO in (0,1)";
        }

        if($nf){
            $nf = " AND FIN_CONTAS.CON_NUMERO like '$nf%'";
            $status     = '';
            $dataI      = '';
            $dataF      = '';
            $cliente    = '';
            $vendedor   = '';
            $tipo       = '';
        }else{
            $nf ='';
        }

        $sql="
            select
                FIN_CONTAS.CON_CODIGO
                ,FIN_CONTAS.CON_TIPO
                ,FIN_CONTAS.CON_NUMERO
                ,FIN_CONTAS.CON_SEQUENCIA
                ,FIN_CONTAS.CON_DT_INCLUSAO
                ,FIN_CONTAS.CON_DT_PAGAMENTO
                ,FIN_CONTAS.CON_DT_VENCIMENTO
                ,CON_VALOR_ORIGINAL
                ,FIN_CONTAS.CON_VALOR_JUROS
                ,FIN_CONTAS.CON_VALOR_MULTA
                ,FIN_CONTAS.CON_VALOR_DESCONTO
                ,FIN_CONTAS.CON_VALOR_OUTRASDESPESAS
                ,FIN_CONTAS.CON_VALOR_TOTAL_PAGO
                ,FIN_CONTAS.CON_VALOR_CORRIGIDO
                ,FIN_CONTAS.CON_BC_COMISSAO
                ,FIN_CONTAS.CON_SITUACAO
                ,CLI.PART_CLIENTE_CODIGO				AS COD_CLI
                ,CLI.PART_NOME							AS CLIENTE
                ,REP.PART_REPRESENTANTE_CODIGO			AS COD_REP
                ,REP.PART_NOME							AS REPRESENTANTE

            FROM FIN_CONTAS
            LEFT JOIN FIN_CONTAS_COMISSAO ON FIN_CONTAS_COMISSAO.CON_CODIGO = FIN_CONTAS.CON_CODIGO
            LEFT JOIN PARTICIPANTE AS CLI on CLI.PART_CLIENTE_CODIGO = FIN_CONTAS.ENT_CODIGO
            LEFT JOIN PARTICIPANTE AS REP ON REP.PART_REPRESENTANTE_CODIGO = FIN_CONTAS_COMISSAO.PART_REPRESENTANTE_CODIGO

            WHERE CON_TIPO = 0
            AND FIN_CONTAS.CON_PREVISAO = 0
            $nf
            $status
            $dtIEmissa
            $dtFEmissa
            $dataI
            $dataF
            $cliente
            $vendedor
            $tipo
			GROUP BY
                FIN_CONTAS.CON_CODIGO
                ,FIN_CONTAS.CON_TIPO
                ,FIN_CONTAS.CON_NUMERO
                ,FIN_CONTAS.CON_SEQUENCIA
                ,FIN_CONTAS.CON_DT_INCLUSAO
                ,FIN_CONTAS.CON_DT_PAGAMENTO
                ,FIN_CONTAS.CON_DT_VENCIMENTO
                ,CON_VALOR_ORIGINAL
                ,FIN_CONTAS.CON_VALOR_JUROS
                ,FIN_CONTAS.CON_VALOR_MULTA
                ,FIN_CONTAS.CON_VALOR_DESCONTO
                ,FIN_CONTAS.CON_VALOR_OUTRASDESPESAS
                ,FIN_CONTAS.CON_VALOR_TOTAL_PAGO
                ,FIN_CONTAS.CON_VALOR_CORRIGIDO
                ,FIN_CONTAS.CON_BC_COMISSAO
                ,FIN_CONTAS.CON_SITUACAO
                ,CLI.PART_CLIENTE_CODIGO
                ,CLI.PART_NOME
                ,REP.PART_REPRESENTANTE_CODIGO
                ,REP.PART_NOME
            ORDER BY
                REP.PART_REPRESENTANTE_CODIGO
                ,CON_DT_VENCIMENTO
                ,CLI.PART_NOME
        ";
        $receber = dB::connection(env('APP_NAME'))->select($sql);
        return $receber;
    }


}
