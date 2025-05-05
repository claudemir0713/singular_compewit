<?php
namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Vendas {
    public static function Totvs_receber($serie,$doc) {
        $sql = "
                SELECT
                    0 												AS CON_CODIGO
                    ,0 												AS CON_TIPO
                    ,CONCAT(CAST(E1_NUM AS INT),'/',E1_SERIE)  		AS CON_NUMERO
                    ,CAST(E1_PARCELA AS INT) 						AS CON_SEQUENCIA
                    ,'2025-03-06' 									AS CON_DT_INCLUSAO
                    ,convert(CHAR(10),CONVERT(DATE,E1_VENCTO),126)	AS CON_DT_VENCIMENTO
                    ,CASE
                        WHEN LEFT(E1_PESSOA,1)='C' THEN REPLACE(E1_PESSOA,'C',1)
                        WHEN LEFT(E1_PESSOA,1)='F' THEN REPLACE(E1_PESSOA,'F',2)
                        WHEN LEFT(E1_PESSOA,1)='T' THEN REPLACE(E1_PESSOA,'T',3)
                        WHEN LEFT(E1_PESSOA,1)='V' THEN REPLACE(E1_PESSOA,'V',4)
                    END 											AS ENT_CODIGO
                    ,1												AS ENT_TIPO
                    ,E1_VALOR 										AS CON_VALOR_ORIGINAL
                    ,0 												AS CON_VALOR_JUROS
                    ,0 												AS CON_VALOR_MULTA
                    ,0 												AS CON_VALOR_DESCONTO
                    ,0 												AS CON_VALOR_OUTRASDESPESAS
                    ,0 												AS CON_VALOR_TOTAL_PAGO
                    ,E1_VALOR 										AS CON_VALOR_CORRIGIDO
                    ,0 												AS CON_SITUACAO
                    ,0 												AS CON_PREVISAO
                    ,1 												AS CON_TIPO_PAGAMENTO
                    ,2 												AS CON_TX_JUROS_MORA
                    ,0 												AS CON_CARENCIA_JUROS_MORA
                    ,2 												AS CON_TX_MULTA
                    ,0 												AS CON_CARENCIA_MULTA
                    ,cast(cast(E1_OBSERV as varbinary(max)) as varchar(max)) AS CON_OBS
                    ,'A' 											AS CON_ORIGEM
                    ,0                                              AS DOC_NUMERO
                    ,0                                              AS DOC_NUMERO_ORIGEM
                    ,convert(CHAR(10),CONVERT(DATE,E1_EMISSAO),126) AS CON_DT_COMPETENCIA

                    ,CASE
                        WHEN LEFT(E1_VEND1,1)='C' THEN REPLACE(E1_VEND1,'C',1)
                        WHEN LEFT(E1_VEND1,1)='F' THEN REPLACE(E1_VEND1,'F',2)
                        WHEN LEFT(E1_VEND1,1)='T' THEN REPLACE(E1_VEND1,'T',3)
                        WHEN LEFT(E1_VEND1,1)='V' THEN REPLACE(E1_VEND1,'V',4)
                    END 											AS PART_CODIGO
                    ,E1_VALMERC										AS CON_BC_COMISSAO
                    ,'2025-03-06 00:00:00' 							AS NOT_DATA_HORA_ALTER_SITUACAO
                    ,se1.E1_COMIS1									AS PERC_COMIS
                FROM se1
                WHERE D_E_L_E_T_ <> '*'
                AND E1_VENCTO >='20250401'
                AND E1_SERIE = $serie
                AND E1_NUM = $doc
        ";
        // dd($sql);
        $return = DB::connection('totvs')->select($sql);
        return $return;
    }


}
