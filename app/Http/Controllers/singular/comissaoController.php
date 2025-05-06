<?php

namespace App\Http\Controllers\singular;

use App\Helpers\Comissao;
use App\Helpers\Helper;
use App\Helpers\Totvs_receber;
use App\Http\Controllers\Controller;
use App\Models\FIN_CONTAS;
use App\Models\FIN_CONTAS_COMISSAO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class comissaoController extends Controller
{
    public function listAll(Request $request){
        $dateForm = $request->except('_token');

        $nf         = $request->nf;
        $dataI      = $request->dtI;
        $dataF      = $request->dtF;
        if(!$dataI){$dataI = date('Y-m-d');};
        if(!$dataF){$dataF = date('Y-m-d');};

        $vendedor   = strtoupper($request->vendedor);
        $cliente    = strtoupper($request->cliente);

        $comissoes = Comissao::comissao($dataI,$dataF,$cliente,$vendedor,$nf);
        return view('comissao.listAll',compact('comissoes','dateForm'));
    }

    public function alteraBase(Request $request)
    {
        $return = 'success';
        try{
            $fin_contas = FIN_CONTAS::find($request->con_codigo);
            $fin_contas->CON_BC_COMISSAO = $request->con_bc_comissao;
            $fin_contas->save();
        }catch(\Exception $e){
            $return = $e;
        }
        return response()->json($return);
    }

    public function comissaoPagar(Request $request){
        $dateForm = $request->except('_token');

        $nf         = $request->nf;
        $parcela    = $request->parcela;
        $dataI      = $request->dtI;
        $dataF      = $request->dtF;
        if(!$dataI){$dataI = date('Y-m-d');};
        if(!$dataF){$dataF = date('Y-m-d');};


        $vendedor   = strtoupper($request->vendedor);
        $cliente    = strtoupper($request->cliente);
        $ordenar    = $request->ordenar;

        $comissoes = Comissao::comissaoPagar($dataI,$dataF,$cliente,$vendedor,$nf,$parcela,$ordenar);
        return view('comissao.comissaoPagar',compact('comissoes','dateForm'));
    }

    public function imprimirComissaoPagar(Request $request){
        $nf         = $request->nf;
        $parcela    = $request->parcela;
        $dataI      = $request->dtI;
        $dataF      = $request->dtF;
        if(!$dataI){$dataI = date('Y-m-d');};
        if(!$dataF){$dataF = date('Y-m-d');};

        $vendedor   = strtoupper($request->vendedor);
        $cliente    = strtoupper($request->cliente);
        $ordenar    = $request->ordenar;

        $comissoes = Comissao::comissaoPagar($dataI,$dataF,$cliente,$vendedor,$nf,$parcela,$ordenar);
        $fileName = 'Comissão pagar.pdf';
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'A4',
            'margin_left'   => 15,
            'margin_rigth'  => 10,
            'margin_top'    => 20,
            'margin_bottom' => 15,
            'margin_header' => 5,
            'margin_footer' => 5
        ]);

        $cabecalho = '<table width="100%">';
        $cabecalho .='<tr>';
        $cabecalho .='<td width="10%" align="center"><img src="'.asset('img/'.env('APP_NAME').'.png').'" height="30"></td>';
        $cabecalho .= '<td width="90%" align="center"><span style="font-size:20px"><b>Comissões a pagar</b></span></td>';
        $cabecalho .='</tr>';
        $cabecalho .='</table><hr>';

        $rodape = '<hr><table width="100%">';
        $rodape .='<tr>';
        $rodape .='<td width="80%" align="center"></td>';
        $rodape .= '<td width="20%" align="right"><span style="font-size:10px">Página {PAGENO} de {nb}</span></td>';
        $rodape .='</tr>';
        $rodape .='</table>';

        $html = view('comissao.imprimePdfComissaoPagar', compact('comissoes'));
        $html->render();
        $mpdf->SetHTMLHeader($cabecalho);
        $mpdf->SetHTMLFooter($rodape);
        $mpdf->AddPage('P');
        $mpdf->WriteHTML($html);
        $mpdf->Output($fileName, 'I');
        exit();
    }

    public function listaRecTotvs(Request $request)
    {
        return view('comissao.importaRecTotvs');
    }

    public function importaRecTotvs(Request $request)
    {
        $serie  = $request->serie;
        $doc    = $request->documento;

        $return = Totvs_receber::Totvs_receber($serie,$doc);

        foreach($return as $item){
            $sql = "SELECT max(DOC_NUMERO)+1 AS DOC_NUMERO  FROM DOCUMENTO";
            $DOC_NUMERO = DB::connection(env('APP_NAME'))->select($sql);

            $sql = "SELECT max(CON_CODIGO)+1 AS CON_CODIGO  FROM FIN_CONTAS";
            $CON_CODIGO = DB::connection(env('APP_NAME'))->select($sql);

            $sql = "SELECT max(CONC_CODIGO)+1 AS CONC_CODIGO  FROM FIN_CONTAS_COMISSAO ";
            $CONC_CODIGO = DB::connection(env('APP_NAME'))->select($sql);

            try{
                $FIN_CONTAS = new FIN_CONTAS([
                    'CON_CODIGO'                  => $CON_CODIGO
                    , 'CON_TIPO'                  => $item->CON_TIPO
                    , 'CON_NUMERO'                => $item->CON_NUMERO
                    , 'CON_SEQUENCIA'             => $item->CON_SEQUENCIA
                    , 'CON_DT_INCLUSAO'           => $item->CON_DT_INCLUSAO
                    , 'CON_DT_VENCIMENTO'         => $item->CON_DT_VENCIMENTO
                    , 'ENT_CODIGO'                => $item->ENT_CODIGO
                    , 'ENT_TIPO'                  => $item->ENT_TIPO
                    , 'CON_VALOR_ORIGINAL'        => $item->CON_VALOR_ORIGINAL
                    , 'CON_VALOR_JUROS'           => $item->CON_VALOR_JUROS
                    , 'CON_VALOR_MULTA'           => $item->CON_VALOR_MULTA
                    , 'CON_VALOR_OUTRASDESPESAS'  => $item->CON_VALOR_OUTRASDESPESAS
                    , 'CON_VALOR_TOTAL_PAGO'      => $item->CON_VALOR_TOTAL_PAGO
                    , 'CON_VALOR_CORRIGIDO'       => $item->CON_VALOR_CORRIGIDO
                    , 'CON_SITUACAO'              => $item->CON_SITUACAO
                    , 'CON_PREVISAO'              => $item->CON_PREVISAO
                    , 'CON_TIPO_PAGAMENTO'        => $item->CON_TIPO_PAGAMENTO
                    , 'CON_TX_JUROS_MORA'         => $item->CON_TX_JUROS_MORA
                    , 'CON_CARENCIA_JUROS_MORA'   => $item->CON_CARENCIA_JUROS_MORA
                    , 'CON_TX_MULTA'              => $item->CON_TX_MULTA
                    , 'CON_CARENCIA_MULTA'        => $item->CON_CARENCIA_MULTA
                    , 'CON_OBS'                   => $item->CON_OBS
                    , 'CON_ORIGEM'                => $item->CON_ORIGEM
                    , 'DOC_NUMERO'                => $DOC_NUMERO
                    , 'DOC_NUMERO_ORIGEM'         => $DOC_NUMERO
                    , 'CON_DT_COMPETENCIA'        => $item->CON_DT_COMPETENCIA
                    , 'PART_CODIGO'               => $item->PART_CODIGO
                    , 'CON_BC_COMISSAO'           => $item->CON_BC_COMISSAO
                    , 'NOT_DATA_HORA_ALTER_SITUACAO'=> $item->NOT_DATA_HORA_ALTER_SITUACAO
                ]);
                // $FIN_CONTAS->save();

                $FIN_CONTAS_COMISSAO = new FIN_CONTAS_COMISSAO([
                    'CONC_CODIGO'                 => $CONC_CODIGO
                    , 'CON_CODIGO'                => $CON_CODIGO
                    , 'USU_COD_VENDEDOR'          => 0
                    , 'PART_REPRESENTANTE_CODIGO' => $item->PART_CODIGO
                    , 'CONC_PERC_COMISSAO'        => $item->PERC_COMIS
                ]);
                // $FIN_CONTAS_COMISSAO->save();
                dd($DOC_NUMERO,$CON_CODIGO,$CONC_CODIGO,$item,$FIN_CONTAS,$FIN_CONTAS_COMISSAO);

            }catch(\Exception $e){

            }

            print_r($DOC_NUMERO.' - '.$CON_CODIGO.' - '.$CONC_CODIGO."\n");

        }

    }

}
