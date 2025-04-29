<?php

namespace App\Http\Controllers\singular;

use App\Helpers\Receber;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class receberController extends Controller
{
    public function listAll(Request $request){
        $dateForm = $request->except('_token');
        $dtIEmissa  = $request->dtIEmissa;
        $dtFEmissa  = $request->dtFEmissa;

        $dataI      = $request->dtI;
        $dataF      = $request->dtF;
        if(!$dataI){$dataI = date('Y-m-d');};
        if(!$dataF){$dataF = date('Y-m-d');};
        $cliente   = strtoupper($request->cliente);
        $vendedor  = strtoupper($request->vendedor);
        $tipo      = strtoupper($request->tipo);
        $status    = strtoupper($request->status);
        $nf        = strtoupper($request->nf);

        $receber = Receber::receber($dataI,$dataF,$cliente,$vendedor,$tipo,$status,$nf,$dtIEmissa,$dtFEmissa);
        return view('receber.listAll',compact('receber','dateForm'));
    }

    public function imprimir(Request $request){
        $dtIEmissa  = $request->dtIEmissa;
        $dtFEmissa  = $request->dtFEmissa;

        $dataI      = $request->dtI;
        $dataF      = $request->dtF;
        if(!$dataI){$dataI = date('Y-m-d');};
        if(!$dataF){$dataF = date('Y-m-d');};
        $cliente   = strtoupper($request->cliente);
        $vendedor  = strtoupper($request->vendedor);
        $tipo      = strtoupper($request->tipo);
        $status    = strtoupper($request->status);
        $nf        = strtoupper($request->nf);

        $receber = Receber::receber($dataI,$dataF,$cliente,$vendedor,$tipo,$status,$nf,$dtIEmissa,$dtFEmissa);
        $fileName = 'Contas a receber.pdf';
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
        $cabecalho .='<td width="10%" align="center"><img src="'.asset('img/logo.png').'" height="30"></td>';
        $cabecalho .= '<td width="90%" align="center"><span style="font-size:20px"><b>Contas a receber</b></span></td>';
        $cabecalho .='</tr>';
        $cabecalho .='</table><hr>';


        $rodape = '<hr><table width="100%">';
        $rodape .='<tr>';
        $rodape .='<td width="80%" align="center"></td>';
        $rodape .= '<td width="20%" align="right"><span style="font-size:10px">Página {PAGENO} de {nb}</span></td>';
        $rodape .='</tr>';
        $rodape .='</table>';

        $html = view('receber.imprimePdf', compact('receber'));
        $html->render();
        $mpdf->SetHTMLHeader($cabecalho);
        $mpdf->SetHTMLFooter($rodape);
        $mpdf->AddPage('L');
        $mpdf->WriteHTML($html);
        $mpdf->Output($fileName, 'I');
    }
}
