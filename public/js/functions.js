/**********************formata numero **************************************************/
const formCurrency = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
    minimumFractionDigits: 2
})

/*********************converte valor texto para valor************************************/
function formataValor(valor){
    let vlr_txt = valor.replaceAll('.','').replaceAll(',','.');
    let vlr_float = parseFloat(vlr_txt)
    return vlr_float
}
/********************* busca cep cliente *****************************************/
function buscaCep(cep){
    $.ajax({
        data: {cep:cep},
        type: 'POST',
        dataType: 'JSON',
        url:url+'/clientes/buscaCep',
        beforeSend: function(){

        },
        success: function(result)
        {
            $('#Mun').val(result.localidade);
            $('#Ender').val(result.logradouro);
            $('#Bairro').val(result.bairro);
            $('#Uf').val(result.uf);
        }
    });

}

/*****************************busca cnpj*****************************************/
function buscaCnpj(cnpj){
    $.ajax({
        data: {cnpj:cnpj},
        type: 'POST',
        dataType: 'JSON',
        url:url+'/clientes/buscaCnpj',
        beforeSend: function(){
            Swal({
                title: 'Aguarde consultado dados!',
                type: 'warning',
                timer:2000
            })
        },
        success: function(result)
        {
            $('input#nome').val(result.nome);
            $('input#cep').val(result.cep);
            $('input#telefone').val(result.telefone);
            $('input#cidade').val(result.municipio);
            $('input#email').val(result.email);
            $('input#endereco').val(result.logradouro+','+result.numero);
            $('input#bairro').val(result.bairro);
            $('input#uf').val(result.uf);

        }
    });
}


function atualizaCards(){
    $.ajax({
        data: '',
        type: 'post',
        url:url+'/home/atualizaCard',
        dataType: 'JSON',
        error: function(result){
        },
        success: function(result)
        {
            $.each(result, function(i, val){
                $('#span-nr'+val.etapa.replace(/\s/g, '')).html(val.qtd)
            });
        }

    })
}

/*******************************calcula PMT **************************************/
function PMT(ir,np, pv, fv = 0){
    var  fator = 0;
    fator = Math.pow((1 + ir), np);
    var pmt = ir * pv  * (fator + fv)/(fator-1);
    return pmt;
}

/***********************************cadastrar************************************ */
function cadastrar(dados,route,type,origem){
    var title = 'Cadastro alterado com sucesso!';
    if(type == 'POST'){
        title = 'Cadastro efetuado com sucesso!';
    }
    var tipo = 'success';
    let tempo = 500;
    $.ajax({
        data: dados,
        type: type,
        dataType: 'JSON',
        url:url+route,
        success: function(result)
        {
            if(result=="Existe"){
                title="Já existe esse cadastro";
                tipo = 'info';
                tempo = 2000;
            }else if(result!="success"){
                title="Cadastro não efetuado";
                tipo = 'error';
                tempo = 2000;
                console.log(result)
            }else{
                if(type!='POST'){
                    window.location.replace(url+'/'+origem);
                }else if(origem != 'usuarioBaseNovo'){
                    window.location.reload();
                    $('.limpar').val('');
                    $('select').trigger("chosen:updated");
                }
            }
            Swal({
                title: title,
                type: tipo,
                timer:tempo
            })
    },
        complete: function(){
            // $('#salvar').prop("disabled",false);
        }
    })
}

function liberaMenuDisponivel()
{
    var usuario = $(document).find('#usuario').val();
    var dados = {
        'usuario': usuario
    };
    var route = '/menu/disponivel'
    var linhas = '';
    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        beforeSend : function(){
            linhas = '';
            $('#menuDisponivel').html('');
            swal({
                title: 'Aguarde!',
                type: 'warning',
                html: '<strong>Efetuando busca</strong>',
                onOpen: () => {
                    swal.showLoading()
                }
            })
        },
        success: function (result) {
            linhas = '';
            classe = '';
            $.each(result, function (i, val) {
                if(val.tipo=='Título'){
                    classe='negrito';
                }else{
                    classe='paragrafo';
                };
                var id = 0;
                (val.selecionado=="checked")?id = val.selecionadoId : id=val.disponivelId
                linhas += '<tr>';
                    linhas += '<td class="'+classe+'"><button class="btn btn-link" value="'+val.disponivelId+'">'+val.ordem+'-'+val.descricao+'</button></td>';
                    linhas += '<td>';
                        linhas += '<label class="switch" >';
                            linhas += '<input type="checkbox" class="disponivel" id="protrang" name="protrang" '+val.selecionado+' value="'+id+'">';
                            linhas += '<span class="slider round"></span>';
                        linhas += '</label>';
                    linhas += '</td>';
                linhas += '</tr>';
            })

        },
        complete:function(){
            $('#menuDisponivel').html(linhas);
            swal.close();
        }
    })
}

function removeMenuLiberado()
{
    var usuario = $(document).find('#usuario').val();
    var dados = {
        'usuario': usuario
    };
    var route = '/menu/menuLiberado'
    var linhas = '';
    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        beforeSend : function(){
            linhas = '';
            $('#menuLiberado').html('');
            swal({
                title: 'Aguarde!',
                type: 'warning',
                html: '<strong>Efetuando busca</strong>',
                onOpen: () => {
                    swal.showLoading()
                }
            })
        },
        success: function (result) {
        },
        complete:function(){
            $('#menuLiberado').html(linhas);
            swal.close();
        }
    })
}

function addMenuUsuario(disponivelId,usuario){
    var dados = {
        'usuario': usuario,
        'disponivelId' : disponivelId
    };
    var route = '/menu/addMenuUsuario'
    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        complete:function(){
            liberaMenuDisponivel();
            removeMenuLiberado();
        }
    })
}
function removeMenuUsuario(liberadoId){
    var dados = {
        'liberadoId' : liberadoId
    };
    var route = '/menu/removeMenuUsuario'
    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        complete:function(){
            liberaMenuDisponivel();
            removeMenuLiberado();
        }
    })
}

function ativaUsuario(usuario_id,ativo,route){
    var dados = {
        'usuario_id': usuario_id,
        'ativo' : ativo
    };
    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route
    })
}

function baseComissao(con_codigo,con_bc_comissao,origem){
    let bgcolor = '';
    let valor_original = formataValor($(document).find('.valor_original'+con_codigo).html())
    let baseConvertida = formataValor(con_bc_comissao);
    let ipi_frete = formataValor($(document).find('.ipi_frete'+con_codigo).html())
    let perc_comissao = formataValor($(document).find('.perc_comissao'+con_codigo).html())


    if(origem=='dbclick'){
        baseConvertida = valor_original - ipi_frete;
    }

    if((valor_original.toFixed(2)) != ((baseConvertida+ipi_frete).toFixed(2))){bgcolor = 'red'};

    let comissao = baseConvertida * (perc_comissao/100);
    comissao = formCurrency.format(comissao).replace('R$ ','');
    let baseRetorno = formCurrency.format(baseConvertida).replace('R$ ','');

    $(document).find('.comissao'+con_codigo).html(comissao);
    $(document).find('#'+con_codigo).val(baseRetorno);

    let route = '/comissao/alteraBase';
    let dados = {
        'con_codigo'        : con_codigo,
        'con_bc_comissao'   : baseConvertida
    };
    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        beforeSend: function(){
            Swal({
                title: 'Aguarde!',
                type: 'warning',
                timer:2000
            })
        },
        success:function(result){
            $(document).find('.linha'+con_codigo).attr('bgcolor',bgcolor);
        },
        complete:function(){
            Swal.close();
        }
    })

}

function btnExportaContabil(){
    let dtI = $(document).find('#dtI').val();
    let dtF = $(document).find('#dtF').val();
    let tipo = $(document).find('#tipo').val();
    let arquivo = '';

    if(tipo==0){
        arquivo = 'Contas a Receber'+dtI+'-'+dtF+'.txt';
    }else{
        arquivo = 'Contas a Pagar'+dtI+'-'+dtF+'.txt';
    }

    let route = '/contabilidade/geraArquivoBiaxas';
    let dados = {
        'dtI'        : dtI,
        'dtF'        : dtF,
        'tipo'       : tipo,
    };

    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        beforeSend: function(){
            $(document).find('.linkExportaContabil').html('')
            Swal({
                title: 'Aguarde!',
                type: 'warning',
                // timer:2000
            })
        },
        success:function(result){
            let link = '<h4><a href="download/'+arquivo+'" target="_blank"><span class="fas fa-download"></span> '+arquivo+'</a></h4>';
            console.log(link);
            if(result==1){
                Swal({
                    title: 'Arquivo gerado com sucesso!',
                    type: 'success',
                    timer:2000
                })
                $(document).find('.linkExportaContabil').html(link)
            }
        },
        complete:function(){
        }
    })

}

function btnGeraSped(){
    let dtI = $(document).find('#dtI').val();
    let dtF = $(document).find('#dtF').val();
    let cod_fin = $(document).find('#cod_fin').val();
    let arquivo = 'Sped'+dtI+'-'+dtF+'.txt';

    let route = '/contabilidade/geraSped';
    let dados = {
        'dtI'        : dtI,
        'dtF'        : dtF,
        'cod_fin'    : cod_fin,
    };

    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        beforeSend: function(){
            Swal({
                title: 'Aguarde!',
                type: 'warning',
                // timer:2000
            })
        },
        success:function(result){
            let link = '<h4><a href="download/'+arquivo+'" target="_blank"><span class="fas fa-download"></span> '+arquivo+'</a></h4>';
            console.log(link);
            if(result==1){
                Swal({
                    title: 'Arquivo gerado com sucesso!',
                    type: 'success',
                    timer:2000
                })
                $(document).find('.linkExportaContabil').html(link)
            }
        },
        complete:function(){
        }
    })
}

function btnFechaEstoque(){
    let ano = $(document).find('#ano').val();
    let mes = $(document).find('#mes').val();

    let route = '/contabilidade/geraEstoque';
    let dados = {
        'ano'        : ano,
        'mes'        : mes,
    };

    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        beforeSend: function(){
            Swal({
                title: 'Aguarde!',
                type: 'warning',
                // timer:2000
            })
        },
        success:function(result){
        },
        complete:function(){
            Swal({
                title: 'Arquivo processado!',
                type: 'success',
                timer:2000
            })
        }
    })
}

function btnFechaEstoque(){
    let texto = $(document).find('#texto').val();

    let route = '/contabilidade/geraAcertoEstoque';
    let dados = {
        'texto'        : texto,
    };

    $.ajax({
        data: dados,
        type: 'post',
        dataType: 'JSON',
        url: url + route,
        beforeSend: function(){
            Swal({
                title: 'Aguarde!',
                type: 'warning',
                // timer:2000
            })
        },
        success:function(result){
            console.log(result);
            $(document).find('#texto').val('');
            $(document).find('#texto').val(result)
        },
        complete:function(){
            Swal({
                title: 'Arquivo processado!',
                type: 'success',
                timer:2000
            })
        }
    })
}

