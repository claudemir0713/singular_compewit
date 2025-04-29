@extends('layouts.model')
@section('content')

    <table class="table table-borderless table-advance table-condensed">
        <tr>
            <td width="80%">
                <h3>
                    <i class="fas fa-dollar-sign"></i> Receber
                </h3>
            </td>
        </tr>
    </table><hr>

    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
        <span class="fas fa-filter"></span> Filtros
    </button>
    <button class="btn btn-info" type="button" id="btn-imprimirContasReceber">
        <span class="fas fa-print"></span> Imprimir
    </button>
    <p>
    <div class="collapse" id="collapseExample">
        <div class="card card-body">
            <form method="get" action="{{ route('receber.listAll') }}">
                @csrf
                <div class="row">
                    <div class="form-group col-md-2">
                        Doc:
                        <input class="form-control" type="text" id="nf" name="nf" value="{{ array_key_exists('nf',$dateForm) ? $dateForm['nf'] : '' }}">
                    </div>
                    <div class="form-group col-md-4">
                        Cliente:
                        <input class="form-control" type="text" id="cliente" name="cliente" value="{{ array_key_exists('cliente',$dateForm) ? $dateForm['cliente'] : '' }}">
                    </div>
                    <div class="form-group col-md-4">
                        Vendedor:
                        <input class="form-control" type="text" id="vendedor" name="vendedor" value="{{ array_key_exists('vendedor',$dateForm) ? $dateForm['vendedor'] : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        Tipo:
                        <select class="form-control" id="tipo" name="tipo">
                            <option value="">Todos</option>
                            <option value="NF" {{ ((array_key_exists('tipo',$dateForm) ? $dateForm['tipo'] : '')=='NF') ? 'selected' : '' }}>NF</option>
                            <option value="PD" {{ ((array_key_exists('tipo',$dateForm) ? $dateForm['tipo'] : '')=='PD') ? 'selected' : '' }}>CH/PD</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-2">
                        Emissão de :
                        <input class="form-control" type="date" id="dtIEmissa" name="dtIEmissa" value="{{ array_key_exists('dtIEmissa',$dateForm) ? $dateForm['dtIEmissa'] : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        Emissão até:
                        <input class="form-control" type="date" id="dtFEmissa" name="dtFEmissa" value="{{ array_key_exists('dtFEmissa',$dateForm) ? $dateForm['dtFEmissa'] : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        Vencimento de :
                        <input class="form-control" type="date" id="dtI" name="dtI" value="{{ array_key_exists('dtI',$dateForm) ? $dateForm['dtI'] : date('Y-m-d') }}">
                    </div>
                    <div class="form-group col-md-2">
                        Vencimento até:
                        <input class="form-control" type="date" id="dtF" name="dtF" value="{{ array_key_exists('dtF',$dateForm) ? $dateForm['dtF'] : date('Y-m-d') }}">
                    </div>
                    {{-- <div class="form-group col-md-2">
                        Status:
                        <select class="form-control" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="0,1" {{ ((array_key_exists('status',$dateForm) ? $dateForm['status'] : '')=='0,1') ? 'selected' : '' }}>Abertos</option>
                            <option value="2" {{ ((array_key_exists('status',$dateForm) ? $dateForm['status'] : '')=='2') ? 'selected' : '' }}>Pagos</option>
                        </select>
                    </div> --}}
                </div>
                <button class="btn btn-primary" type="submit" >
                    <span class="fas fa-play"></span> Filtrar
                </button>
            </form >
        </div>
    </div>
    <p>


    <table class="table table-bordered table-condensed fonte-10 courier">
        <thead>
            <tr>
                <th width="5%">NF</th>
                <th width="3%">PARCELA</th>
                <th width="30%">CLIENTE</th>
                <th width="5%">EMISSÃO</th>
                <th width="5%">VENCIMENTO</th>
                <th width="5%">VALOR</th>
                <th width="5%">PAGO</th>
                <th width="5%">MULTA</th>
                <th width="5%">JUROS</th>
                <th width="8%">SALDO</th>
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
@endsection
