<style>
    table,td,th {
        padding: 5px;
        border-collapse: collapse;
        font-family: 'Courier New', Courier, monospace;
        /* font-size: 8px */
    }
    th {
        align:center;
        background: #f3f3f3;
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

    <table class="fonte-10" width='100%'>
        <thead>
            <tr>
                <th width="7%">NF</th>
                <th width="5%">PARCELA</th>
                <th width="38%">CLIENTE</th>
                <th width="5%">EMISSÃO</th>
                <th width="5%">VENCIMENTO</th>
                <th width="5%">VALOR</th>
                <th width="5%">PAGO</th>
                <th width="5%">MULTA</th>
                <th width="5%">JUROS</th>
                <th width="10%">SALDO</th>
            </tr>
        </thead>
        @php
            $COD_REP            = 0;
            $vlr_total          = 0;
            $vlr_total_pago     = 0;
            $vlr_total_juros    = 0;
            $vlr_total_multa    = 0;
            $vlr_total_saldo    = 0;

            $vlr_total_rep      = 0;
            $vlr_total_pago_rep = 0;
            $vlr_total_juros_rep= 0;
            $vlr_total_multa_rep= 0;
            $vlr_total_saldo_rep= 0;
        @endphp
        <tbody>
            @foreach ($receber as $item )
                @if($COD_REP!=$item->COD_REP)
                    @if ($COD_REP!='0')
                        <tr bgcolor="#e3e3e3">
                            <td colspan="5"><b>TOTAL</b></td>
                            <td align="right"><b>{{number_format($vlr_total_rep,2,',','.')}}</b></td>
                            <td align="right"><b>{{number_format($vlr_total_pago_rep,2,',','.')}}</b></td>
                            <td align="right"><b>{{number_format($vlr_total_juros_rep,2,',','.')}}</b></td>
                            <td align="right"><b>{{number_format($vlr_total_multa_rep,2,',','.')}}</b></td>
                            <td align="right"><b>{{number_format($vlr_total_saldo_rep,2,',','.')}}</b></td>
                        </tr>
                        @php
                            $vlr_total_rep = 0;
                            $vlr_total_pago_rep = 0;
                            $vlr_total_juros_rep = 0;
                            $vlr_total_multa_rep = 0;
                            $vlr_total_saldo_rep = 0;
                        @endphp
                    @endif
                    <tr>
                        <td colspan="10" bgcolor="#d3d3d3"><b>{{$COD_REP}} - {{$item->REPRESENTANTE}}</b></td>
                    </tr>
                @endif
                <tr>
                    <td>{{$item->CON_NUMERO}}</td>
                    <td align="center">{{$item->CON_SEQUENCIA}}</td>
                    <td>{{$item->CLIENTE}}</td>
                    <td>{{date('d/m/Y',strtotime($item->CON_DT_INCLUSAO))}}</td>
                    <td>{{date('d/m/Y',strtotime($item->CON_DT_VENCIMENTO))}}</td>
                    <td align="right">{{number_format($item->CON_VALOR_ORIGINAL,2,',','.')}}</td>
                    <td align="right">{{number_format($item->CON_VALOR_TOTAL_PAGO,2,',','.')}}</td>
                    <td align="right">{{number_format($item->CON_VALOR_JUROS,2,',','.')}}</td>
                    <td align="right">{{number_format($item->CON_VALOR_MULTA,2,',','.')}}</td>
                    <td align="right">{{number_format($item->CON_VALOR_CORRIGIDO,2,',','.')}}</td>
                </tr>
                @php
                    $COD_REP            = $item->COD_REP;

                    $vlr_total          += $item->CON_VALOR_ORIGINAL;
                    $vlr_total_pago     += $item->CON_VALOR_TOTAL_PAGO;
                    $vlr_total_juros    += $item->CON_VALOR_JUROS;
                    $vlr_total_multa    += $item->CON_VALOR_MULTA;
                    $vlr_total_saldo    += $item->CON_VALOR_CORRIGIDO;

                    $vlr_total_rep       += $item->CON_VALOR_ORIGINAL;
                    $vlr_total_pago_rep  += $item->CON_VALOR_TOTAL_PAGO;
                    $vlr_total_juros_rep += $item->CON_VALOR_JUROS;
                    $vlr_total_multa_rep += $item->CON_VALOR_MULTA;
                    $vlr_total_saldo_rep += $item->CON_VALOR_CORRIGIDO;
                @endphp
            @endforeach
            <tr bgcolor="#e3e3e3">
                <td colspan="5"><b>TOTAL</b></td>
                <td align="right"><b>{{number_format($vlr_total_rep,2,',','.')}}</b></td>
                <td align="right"><b>{{number_format($vlr_total_pago_rep,2,',','.')}}</b></td>
                <td align="right"><b>{{number_format($vlr_total_juros_rep,2,',','.')}}</b></td>
                <td align="right"><b>{{number_format($vlr_total_multa_rep,2,',','.')}}</b></td>
                <td align="right"><b>{{number_format($vlr_total_saldo_rep,2,',','.')}}</b></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">TOTAL</td>
                <td align="right">{{number_format($vlr_total,2,',','.')}}</td>
                <td align="right">{{number_format($vlr_total_pago,2,',','.')}}</td>
                <td align="right">{{number_format($vlr_total_juros,2,',','.')}}</td>
                <td align="right">{{number_format($vlr_total_multa,2,',','.')}}</td>
                <td align="right">{{number_format($vlr_total_saldo,2,',','.')}}</td>
            </tr>
        </tfoot>
    </table>
