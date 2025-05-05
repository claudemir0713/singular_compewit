<style>
    table,td,th {
        padding: 5px;
        border-collapse: collapse;
        font-family:'Verdana, Helvetica, sans-serif';
        /* font-family: 'Courier New', Courier, monospace; */
        font-size: 8px;
    }
    th {
        align:center;
        background: #f3f3f3;
    }
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #ddd !important;
    }
    .table-line th,
    .table-line td {
        border-bottom: 1px solid #ddd !important;
    }
    .middle{
        vertical-align: middle !important;
    }
    .linha {
        border-bottom: 1px solid #ddd !important;
    }
    .topo {
        border-top: 1px solid #ddd !important;
    }
    .fonte-10{
        font-size: 10px !important;
    }
    .fonte-12{
        font-size: 12px !important;
    }

    .fonte-18{
        font-size: 18px !important;
    }
    .fonte-22{
        font-size: 22px !important;
    }
    .fonte-vermelha{
        color: red !important;
    }
</style>

    @php
        $COD_REP                = '@';
        $tot_vlrOriginal        = 0;
        $tot_baseComissao       = 0;
        $tot_comissao           = 0;
        $tot_vlrOriginal_rep    = 0;
        $tot_baseComissao_rep   = 0;
        $tot_comissao_rep       = 0;

    @endphp
    <tbody>
        {{-- {{dd($comissoes)}} --}}
        @foreach ($comissoes as $item)
            @if($COD_REP!=$item->VENDEDOR)
                @if ($COD_REP!='@')
                    <tr bgcolor="#F3F3F3">
                        <td colspan="5"><b>TOTAL</b></td>
                        <td align="right">{{number_format($tot_vlrOriginal_rep,2,',','.')}}</td>
                        <td align="right">{{number_format($tot_baseComissao_rep,2,',','.')}}</td>
                        <td></td>
                        <td align="right">{{number_format($tot_comissao_rep,2,',','.')}}</td>
                    </tr>
                    </table><pagebreak />
                    @php
                        $tot_vlrOriginal_rep    = 0;
                        $tot_baseComissao_rep   = 0;
                        $tot_comissao_rep       = 0;
                    @endphp

                @endif
                {{-- </table> --}}
                <table class="table-line"  width="100%">
                        <tr>
                            <td colspan="10" bgcolor="#F3F3F3"><b>{{$item->VENDEDOR}}</b></td>
                        </tr>
                        <thead>
                        <tr>
                            <th width="7%">NF</th>
                            <th width="2%">SEQ</th>
                            <th width="20%">CLIENTE</th>
                            <th width="5%">EMISSÃO</th>
                            <th width="7%">VCTO</th>
                            <th width="7%">VLR</th>
                            <th width="6%">BASE</th>
                            <th width="5%">%</th>
                            <th width="8%">R$ Comissão</th>
                            <th width="5%">OBS</th>
                        </tr>
                    </thead>
            @endif

                @php
                    $VLR_PAGO = $item->VLR_PAGO ;
                    $BC_COMISSAO =  $VLR_PAGO *($item->CON_BC_COMISSAO / $item->CON_VALOR_ORIGINAL);
                    if(trim($item->TIPO_BAIXA) =='CHQ'){
                        $CON_NR = $item->CON_NUMERO."\n";
                    }else{
                        $CON_NR = $item->CON_NUMERO;
                    }
                @endphp

                    <tr class="linha{{$item->CON_CODIGO}}">
                        <td align="center">{{$CON_NR}}</td>
                        <td align="center">{{$item->CON_SEQUENCIA}}</td>
                        <td>{{$item->CLIENTE}}</td>
                        <td align="center">{{date('d/m/Y',strtotime($item->CON_DT_INCLUSAO))}}</td>
                        <td align="center">{{date('d/m/Y',strtotime($item->DT_VENCIMENTO))}}</td>
                        <td align="right"><span class="valor_original{{$item->CON_CODIGO}}">{{number_format($VLR_PAGO,2,',','.')}}</span></td>
                        <td align="right"><span class="valor_pago{{$item->CON_CODIGO}}">{{number_format($BC_COMISSAO,2,',','.')}}</span></td>
                        <td align="right"><span class="perc_comissao{{$item->CON_CODIGO}}">{{number_format($item->CONC_PERC_COMISSAO,2,',','.')}}</span></td>
                        <td align="right"><span class="comissao{{$item->CON_CODIGO}}">{{number_format($BC_COMISSAO*($item->CONC_PERC_COMISSAO/100),2,',','.')}}</span></td>
                        <td align="center">{{$item->NR_CHEQUE}}</td>
                    </tr>
                    @php
                    $COD_REP                    = $item->VENDEDOR;

                    $tot_vlrOriginal            += $VLR_PAGO;
                    $tot_baseComissao           += $BC_COMISSAO;
                    $tot_comissao               += $BC_COMISSAO*($item->CONC_PERC_COMISSAO/100);

                    $tot_vlrOriginal_rep        +=  $VLR_PAGO;
                    $tot_baseComissao_rep       += $BC_COMISSAO;
                    $tot_comissao_rep           += $BC_COMISSAO*($item->CONC_PERC_COMISSAO/100);
                    @endphp
            @endforeach
            <tr bgcolor="#e3e3e3">
                <td colspan="5"><b>TOTAL</b></td>
                <td align="right">{{number_format($tot_vlrOriginal_rep,2,',','.')}}</td>
                <td align="right">{{number_format($tot_baseComissao_rep,2,',','.')}}</td>
                <td></td>
                <td align="right">{{number_format($tot_comissao_rep,2,',','.')}}</td>
                <td></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">TOTAL</td>
                <td align="right">{{number_format($tot_vlrOriginal,2,',','.')}}</td>
                <td align="right">{{number_format($tot_baseComissao,2,',','.')}}</td>
                <td></td>
                <td align="right">{{number_format($tot_comissao,2,',','.')}}</td>
                <td></td>
            </tr>
        </tfoot>

</table>
