@extends('layouts.model')
@section('content')

    <table class="table table-borderless table-advance table-condensed">
        <tr>
            <td width="80%">
                <h3>
                    <i class="far fa-handshake"></i> Importa Receber Totvs
                </h3>
            </td>
        </tr>
    </table><hr>

    <div class="row" >
        <div class="card card-body font-12">
            <form method="get" action="{{ route('comissao.listAll') }}">
                @csrf
                <div class="row">
                    <div class="form-group col-md-2">
                        Série:
                        <input class="form-control form-control-sm" type="text" id="serie" name="serie" value="">
                    </div>
                    <div class="form-group col-md-3">
                        Documento:
                        <input class="form-control form-control-sm" type="text" id="documento" name="documento" value="">
                    </div>
                </div>
                <button class="btn btn-primary btn-sm font-10" type="button" >
                    <span class="fas fa-play font-12"></span> <span class="font-12">Importar</span>
                </button>
            </form >
        </div>
    </div>
@endsection
