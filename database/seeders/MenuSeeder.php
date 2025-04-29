<?php

namespace Database\Seeders;

use App\Models\menu;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Promise\Create;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        menu::truncate();
        $menus=[
            [

                'ordem'        =>'01.000'
                , 'descricao'   =>'Cadastros'
                , 'tipo'        =>'Título'
                , 'rota'        =>''
                , 'icone'       =>''
            ],
            [

                'ordem'         =>'01.001'
                , 'descricao'   =>'Menu'
                , 'tipo'        =>'Link'
                , 'rota'        =>'menu.listAll'
                , 'icone'       =>'fa fa-list'
            ],
            [
                'ordem'         =>'01.002'
                , 'descricao'   =>'Menu Usuário'
                , 'tipo'        =>'Link'
                , 'rota'        =>'menu.menuUsuario'
                , 'icone'       =>'fas fa-user-cog'
            ],
            [
                'ordem'         =>'02.000'
                , 'descricao'   =>'Comercial'
                , 'tipo'        =>'Título'
                , 'rota'        =>''
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'02.001'
                , 'descricao'   =>'Vendas'
                , 'tipo'        =>'Link'
                , 'rota'        =>'vendas.listAll'
                , 'icone'       =>'fas fa-dollar-sign'
            ],
            [
                'ordem'         =>'02.002'
                , 'descricao'   =>'Comissões Emissao'
                , 'tipo'        =>'Link'
                , 'rota'        =>'comissao.listAll'
                , 'icone'       =>'far fa-handshake'
            ],
            [
                'ordem'         =>'02.003'
                , 'descricao'   =>'Comissões Pgto'
                , 'tipo'        =>'Link'
                , 'rota'        =>'comissao.comissaoPagar'
                , 'icone'       =>'far fa-handshake'
            ],
            [
                'ordem'         =>'03.000'
                , 'descricao'   =>'Financeiro'
                , 'tipo'        =>'Título'
                , 'rota'        =>''
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'03.001'
                , 'descricao'   =>'Receber'
                , 'tipo'        =>'Link'
                , 'rota'        =>'receber.listAll'
                , 'icone'       =>'fas fa-dollar-sign'
            ],
            [
                'ordem'         =>'03.002'
                , 'descricao'   =>'Pagar'
                , 'tipo'        =>'Link'
                , 'rota'        =>'pagar.listAll'
                , 'icone'       =>'fas fa-dollar-sign'
            ],
            [
                'ordem'         =>'04.000'
                , 'descricao'   =>'Contabil'
                , 'tipo'        =>'Título'
                , 'rota'        =>''
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'04.001'
                , 'descricao'   =>'Exporta Baixas'
                , 'tipo'        =>'Link'
                , 'rota'        =>'contabilidade.exportaBaixas'
                , 'icone'       =>'fas fa-dollar-sign'
            ],
            [
                'ordem'         =>'04.002'
                , 'descricao'   =>'Estoque'
                , 'tipo'        =>'Link'
                , 'rota'        =>'contabilidade.estoque'
                , 'icone'       =>'fas fa-boxes'
            ],
            [
                'ordem'         =>'04.003'
                , 'descricao'   =>'Acerto Estoque'
                , 'tipo'        =>'Link'
                , 'rota'        =>'contabilidade.importaAcertoEstoque'
                , 'icone'       =>'fas fa-hat-wizard'
            ],
            [
                'ordem'         =>'04.004'
                , 'descricao'   =>'Sped'
                , 'tipo'        =>'Link'
                , 'rota'        =>'contabilidade.sped'
                , 'icone'       =>'fas fa-award'
            ],
            [
                'ordem'         =>'99.000'
                , 'descricao'   =>'Totvs Fin.Rec'
                , 'tipo'        =>'Título'
                , 'rota'        =>''
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.001'
                , 'descricao'   =>'Receber por emissão'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_receber/por_emissao.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.002'
                , 'descricao'   =>'Receber aberto cliente'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_receber/por_cliente.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.003'
                , 'descricao'   =>'Receber aberto por Vcto'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_receber/por_vcto.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.004'
                , 'descricao'   =>'Receber aberto por Repr.'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_receber/por_representante.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.005'
                , 'descricao'   =>'Receber recebidas'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_receber/proc_baixa.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.006'
                , 'descricao'   =>'Cheques a receber'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/cheques/index.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.100'
                , 'descricao'   =>'Totvs Fin.Pagar'
                , 'tipo'        =>'Título'
                , 'rota'        =>''
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.101'
                , 'descricao'   =>'Pagar aberto por Forn.'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_pagar/por_fornecedor.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.102'
                , 'descricao'   =>'Pagar aberto por Vcto.'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_pagar/por_vcto.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.103'
                , 'descricao'   =>'Pagar pagas'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_pagar/proc_baixa.php'
                , 'icone'       =>''
            ],

            [
                'ordem'         =>'99.180'
                , 'descricao'   =>'Totvs Fin.Caixa'
                , 'tipo'        =>'Título'
                , 'rota'        =>''
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.181'
                , 'descricao'   =>'Caixa'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/conciliacao/index.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.200'
                , 'descricao'   =>'Totvs Comercial'
                , 'tipo'        =>'Título'
                , 'rota'        =>''
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.201'
                , 'descricao'   =>'Pedidos'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/pedidos/index.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.202'
                , 'descricao'   =>'Histórico cliente'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/contas_receber/hist_cliente.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.203'
                , 'descricao'   =>'Comissão Emitida'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/comissoes/proc_comissao_emitida.php'
                , 'icone'       =>''
            ],
            [
                'ordem'         =>'99.204'
                , 'descricao'   =>'Comissão Gerada'
                , 'tipo'        =>'LinkTotvs'
                , 'rota'        =>'decorbras/restrito/financeiro/comissoes/proc_comissao_gerada.php'
                , 'icone'       =>''
            ],

        ];
        menu::insert($menus);
    }
}
