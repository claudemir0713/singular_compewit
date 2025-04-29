<?php

namespace App\Http\Controllers\singular;

use App\Exports\vendas as ExportsVendas;
use App\Exports\vendasExcel;
use App\Helpers\Vendas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class vendasController extends Controller
{
    public function listAll(Request $request){
        $dateForm = $request->except('_token');

        $dataI      = $request->dtI;
        $dataF      = $request->dtF;
        if(!$dataI){$dataI = date('Y-m-d');};
        if(!$dataF){$dataF = date('Y-m-d');};
        $vendedor   = strtoupper($request->vendedor);
        $cliente    = strtoupper($request->cliente);
        $nf         = $request->nf;

        $vendas = Vendas::vendas($dataI,$dataF,$cliente,$vendedor,$nf);

        if($request->gerar=='Excel'){
            $nomeArquivo = 'vendas.xls';
            return Excel::download(new vendasExcel($vendas,$dateForm), $nomeArquivo);
        };

        return view('vendas.listAll',compact('vendas','dateForm'));

    }
}
