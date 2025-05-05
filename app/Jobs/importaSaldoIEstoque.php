<?php

namespace App\Jobs;

use App\Models\PLANNER_ESTOQUE_BLOCO_K;
use App\Models\sigular_estoque_bloco_k;
use App\Models\singular_estoque_bloco_k;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class importaSaldoIEstoque implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $sql = "
                SELECT
                        CASE
                            WHEN LEFT(B9_CODPROD,3) = '*MP'		THEN concat('91', substring(B9_CODPROD,4,7))
                            WHEN LEFT(B9_CODPROD,2) = 'IM' 		THEN concat('92', substring(B9_CODPROD,3,7))
                            WHEN LEFT(B9_CODPROD,2) = 'MC' 		THEN concat('93', substring(B9_CODPROD,3,7))
                            WHEN LEFT(B9_CODPROD,2) = 'MP' 		THEN concat('94', substring(B9_CODPROD,3,7))
                            WHEN LEFT(B9_CODPROD,5) = 'PROC-' 	THEN concat('95',substring(B9_CODPROD,6,7))
                            WHEN LEFT(B9_CODPROD,1) = 'S' 		THEN concat('96',substring(B9_CODPROD,2,7))
                            ELSE B9_CODPROD
						END  				AS PRD_CODIGO
                        ,LTRIM(RTRIM(SB1.B1_DESCRI)) AS DESC_PROD
                        ,SUM(SB9.B9_QINI)			 AS QTD_EI
                        ,SUM(SB9.B9_VINI1)			 AS TOT_EI
                        ,CONVERT(DATE, SB9.B9_DATA) AS DT_FECHAMENTO
                            FROM SB9
                        LEFT JOIN SB1 ON SB1.B1_CODPROD = SB9.B9_CODPROD
                                    AND LEFT(SB1.B1_FILIAL,2) = LEFT(SB9.B9_FILIAL,2)
                                    AND SB1.D_E_L_E_T_ <> '*'

                    WHERE SB9.D_E_L_E_T_ <> '*'
                    AND sb1.B1_TIPO IN ('2','3')
                    AND SB9.B9_LOCAL = '01'
                    AND YEAR(SB9.B9_DATA) = 2025
                    AND MONTH(SB9.B9_DATA) = 3
                    AND SB9.B9_QINI >0
                    AND DAY(SB9.B9_DATA) = (SELECT DAY(MAX(SB91.B9_DATA))
                                            FROM SB9 SB91
                                            WHERE YEAR(SB91.B9_DATA) = YEAR(SB9.B9_DATA) AND MONTH(SB91.B9_DATA) = MONTH(SB9.B9_DATA)
                                            )
                    GROUP BY SB9.B9_CODPROD
                            ,SB1.B1_DESCRI
                            ,SB9.B9_DATA

        ";
        $est = DB::connection('totvs')->select($sql);
        $x = 0;
        foreach($est as $item){
            $x++;
            try{
                $estoque = new singular_estoque_bloco_k([
                    'prd_codigo'  => $item->PRD_CODIGO
                    , 'qtd'         => number_format($item->QTD_EI,2,'.','')
                    , 'valor'       => number_format($item->TOT_EI,2,'.','')
                    , 'data'        => $item->DT_FECHAMENTO
                ]);
                $estoque->save();
            }catch(\Exception $e){
                dd($e);
            }
            print_r($x.'-'.$item->PRD_CODIGO.'-'.number_format($item->QTD_EI,2,'.','').'-'.number_format($item->TOT_EI,2,'.','')."\n");
        }

    }
}
