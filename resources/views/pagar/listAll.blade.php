@extends('layouts.model')
@section('content')

    <table class="table table-borderless table-advance table-condensed">
        <tr>
            <td width="80%">
                <h3>
                    <i class="fas fa-dollar-sign"></i> Pagar
                </h3>
            </td>
        </tr>
    </table><hr>

    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
        <span class="fas fa-filter"></span> Filtros
    </button><p>
    <div class="collapse" id="collapseExample">
        <div class="card card-body">
            <form method="get" action="{{ route('pagar.listAll') }}">
                @csrf
                <div class="row">
                    <div class="form-group col-md-4">
                        Fornecedor:
                        <input class="form-control" type="text" id="fornecedor" name="fornecedor" value="{{ array_key_exists('fornecedor',$dateForm) ? $dateForm['fornecedor'] : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        De:
                        <input class="form-control" type="date" id="dtI" name="dtI" value="{{ array_key_exists('dtI',$dateForm) ? $dateForm['dtI'] : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        Até:
                        <input class="form-control" type="date" id="dtF" name="dtF" value="{{ array_key_exists('dtF',$dateForm) ? $dateForm['dtF'] : '' }}">
                    </div>
                    <div class="form-group col-md-2">
                        Tipo:
                        <select class="form-control" id="tipo" name="tipo" multiple>
                            <option value="">Todos</option>
                            <option value="NF">NF</option>
                            <option value="PD">CH/PD</option>
                        </select>
                    </div>
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
                <th width="5%">TIPO</th>
                <th width="26%">VENDEDOR</th>
                <th width="30%">CLIENTE</th>
                <th width="5%">EMISSÃO</th>
                <th width="5%">QTD</th>
                <th width="8%">FAT1</th>
                <th width="8%">FAT2</th>
                <th width="8%">FAT.TOTAL</th>
            </tr>
        </thead>
        @php
            $qtd_total = 0;
            $fat_total1 = 0;
            $fat_total2 = 0;
        @endphp
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">TOTAL</td>
                <td align="right">{{number_format($qtd_total,4,',','.')}}</td>
                <td align="right">{{number_format($fat_total1,2,',','.')}}</td>
                <td align="right">{{number_format($fat_total2,2,',','.')}}</td>
                <td align="right">{{number_format($fat_total1+$fat_total2,2,',','.')}}</td>
            </tr>
        </tfoot>
    </table>
@endsection
