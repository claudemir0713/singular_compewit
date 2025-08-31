<?php

namespace App\Http\Controllers\fechamento;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Ficha;
use App\Helpers\Mlc;
use App\Models\mlc_hora;
use App\Models\mlc_horaprod;
use App\Models\produto_ficha;
use Illuminate\Support\Facades\DB;


class fechamentoController extends Controller
{
    public function listAll(Request $request){
        $dateForm = $request->except('_token');
        return view('fechamento.listAll',compact('dateForm'));
    }
    public function fechamento(Request $request){
        $ano    =$request->ano;
        $mes    =$request->mes;
        $mostrar=$request->mostrar;
        $sql="
            SELECT
                COD_PRODUTO,PRODUTO
            FROM(
                /******************VENDA COM DIFERENÇA NO VALOR***************************************/
                    SELECT
                        'SISTEMA' 									AS ORIGEM
                        ,'VENDA' 									AS TIPO
                        ,'NF' 								        AS TIPO_RECEITA
                        ,'DECORBRAS' 			   					AS EMPRESA
                        ,NF.NF_NUMERO_REAL	   		   				AS DOCUMENTO
                        ,NF.NS_SERIE			   					AS SERIE_FAT
                        ,NF.NF_DT_EMISSAO 		   	 				AS DATA_COMP
                        ,DAV.DAV_NUMERO_AUXILIAR					AS NUM_PED
                        ,DAV.DAV_DATA		   						AS DATA_PED
                        ,NF.ENT_CODIGO 				   				AS COD_CLIENTE
                        ,NF.ENT_NOME 			   	   				AS CLIENTE
                        ,NF.NF_EMISSAO_CEP         					AS CEP_IBGE
                        ,CLI.PART_ENDERECO_LOGRADOURO				AS ENDERECO
                        ,CLI.PART_ENDERECO_BAIRRO					AS BAIRRO
                        ,NF_EMISSAO_CIDADE		   	   				AS MUNICIPIO
                        ,NF_EMISSAO_UF			   	   				AS ESTADO
                        ,PAIS.PAIS_NOME				  				AS PAIS
                        ,NF.PART_REPRESENTANTE_CODIGO  				AS COD_VENDEDOR
                        ,REP.PART_NOME								AS VENDEDOR
                        ,COND_PGTO.CPA_DESCRICAO					AS FORMA_DE_PAGTO
                        ,ITENS.PRD_CODIGO							AS COD_PRODUTO
                        ,ITENS.NI_DESCRICAO			  	 			AS PRODUTO
                        ,NI_UNIMED 									AS UN_MEDIDA
                        ,ITENS.NI_QTDE								AS QUANTIDADE
                        ,NI_VL_UNITARIO
                        ,DAVI_VL_UNIT
                        ,ITENS.NI_VL_TOTAL 				 			AS FAT_BRUTO
                        ,CASE
                            WHEN ((DAVI_VL_TOTAL/DAVI_QTDE)-NI_VL_UNITARIO) >0 THEN ((DAVI_VL_TOTAL/DAVI_QTDE)-NI_VL_UNITARIO)  * NI_QTDE
                            ELSE 0
                        END											AS FAT_BRUTO_2
                        ,ITENS.NI_VL_DESCONTO			 			AS DESCONTO
                        , 0 										AS ACRESCIMO
                        ,ITENS.NI_VL_TOTAL-ITENS.NI_VL_DESCONTO  	AS FAT_LIQUIDO
                        ,CFOP.CFOP_CODIGO							AS CFOP
                        ,ITENS.NI_VL_IPI				 			AS IPI
                        ,ITENS.NI_VL_PIS							AS PIS
                        ,ITENS.NI_VL_COFINS							AS COFINS
                        ,ITENS.NI_VL_ICMS               			AS ICMS
                        ,NF.NF_VL_CSLL								AS CSLL
                        ,0											AS IRPJ
                        ,NF.NF_VL_ISS 								AS ISS
                        ,0 											AS PERC_COMISSAO
                        ,ITENS.NI_COMISSAO              			AS COMISSAO
                        ,NF.NF_VL_FRETE								AS FRETE
                        ,(ITENS.NI_VL_TOTAL/NF.NF_VL_PRODUTOS) * DAV.DAV_VALOR_FRETE  AS FRETE_DAV
                        ,NF.NF_NUMERO								AS ID
                        ,'VENDA COM DIFERENÇA NO VALOR'				AS ORIGEM_COMANDO

                    FROM NF_VENDA_CABECALHO NF
                    LEFT JOIN NF_VENDA_ITENS ITENS ON ITENS.NF_NUMERO = NF.NF_NUMERO
                    LEFT JOIN CFOP ON CFOP.CFOP_CODIGO_NF = ITENS.CFOP_CODIGO_NF
                    LEFT JOIN CFOP_GRUPOS ON CFOP_GRUPOS.CFGR_CODIGO = CFOP.CFGR_CODIGO

                    LEFT JOIN dav ON dav.DAV_NUMERO = ITENS.PED_NUMERO
                    LEFT JOIN dav_itens ON dav_itens.DAV_NUMERO = dav.DAV_NUMERO
                                        AND dav_itens.DAVI_ITEM = ITENS.DAVI_ITEM_IMPORTADO

                    LEFT JOIN PAIS ON PAIS.PAIS_CODIGO = NF.PAIS_CODIGO
                    LEFT JOIN PARTICIPANTE AS REP ON REP.PART_REPRESENTANTE_CODIGO = NF.PART_REPRESENTANTE_CODIGO
                    LEFT JOIN PARTICIPANTE AS CLI ON CLI.PART_CODIGO = NF.ENT_CODIGO


                    LEFT JOIN CONDICOES_PAGAMENTO COND_PGTO ON COND_PGTO.CPA_CODIGO = NF.CON_TIPO_PAGAMENTO

                    WHERE 1=1
                    AND (CFOP_GRUPOS.CFGR_CODIGO = 1 OR RIGHT(trim(ITENS.CFOP_CODIGO),3) = '124')
                    AND NF_SITUACAO IN(1)
                    AND dav.DAV_NUMERO IS NOT NULL
                    AND coalesce(DAV.DAV_SITUACAO_ENTREGA,0) <> 1
                    AND extract(year FROM NF_DT_EMISSAO) = $ano
                    AND extract(month FROM NF_DT_EMISSAO) = $mes

                /******************VENDA SEM PEDIDO***************************************/
                    UNION ALL
                    SELECT
                        'SISTEMA' 									AS ORIGEM
                        ,'VENDA' 									AS TIPO
                        ,'NF' 								        AS TIPO_RECEITA
                        ,'DECORBRAS' 			   					AS EMPRESA
                        ,NF.NF_NUMERO_REAL	   		   				AS DOCUMENTO
                        ,NF.NS_SERIE			   					AS SERIE_FAT
                        ,NF.NF_DT_EMISSAO 		   	 				AS DATA_COMP
                        ,0					                        AS NUM_PED
                        ,''		   						            AS DATA_PED
                        ,NF.ENT_CODIGO 				   				AS COD_CLIENTE
                        ,NF.ENT_NOME 			   	   				AS CLIENTE
                        ,NF.NF_EMISSAO_CEP         					AS CEP_IBGE
                        ,CLI.PART_ENDERECO_LOGRADOURO				AS ENDERECO
                        ,CLI.PART_ENDERECO_BAIRRO					AS BAIRRO
                        ,NF_EMISSAO_CIDADE		   	   				AS MUNICIPIO
                        ,NF_EMISSAO_UF			   	   				AS ESTADO
                        ,PAIS.PAIS_NOME				  				AS PAIS
                        ,NF.PART_REPRESENTANTE_CODIGO  				AS COD_VENDEDOR
                        ,REP.PART_NOME								AS VENDEDOR
                        ,COND_PGTO.CPA_DESCRICAO					AS FORMA_DE_PAGTO
                        ,ITENS.PRD_CODIGO							AS COD_PRODUTO
                        ,ITENS.NI_DESCRICAO			  	 			AS PRODUTO
                        ,NI_UNIMED 									AS UN_MEDIDA
                        ,ITENS.NI_QTDE								AS QUANTIDADE
                        ,NI_VL_UNITARIO
                        ,0
                        ,ITENS.NI_VL_TOTAL 				 			AS FAT_BRUTO
                        ,0											AS FAT_BRUTO_2
                        ,ITENS.NI_VL_DESCONTO			 			AS DESCONTO
                        , 0 										AS ACRESCIMO
                        ,ITENS.NI_VL_TOTAL-ITENS.NI_VL_DESCONTO  	AS FAT_LIQUIDO
                        ,CFOP.CFOP_CODIGO							AS CFOP
                        ,ITENS.NI_VL_IPI				 			AS IPI
                        ,ITENS.NI_VL_PIS							AS PIS
                        ,ITENS.NI_VL_COFINS							AS COFINS
                        ,ITENS.NI_VL_ICMS               			AS ICMS
                        ,NF.NF_VL_CSLL								AS CSLL
                        ,0											AS IRPJ
                        ,NF.NF_VL_ISS 								AS ISS
                        ,0 											AS PERC_COMISSAO
                        ,ITENS.NI_COMISSAO              			AS COMISSAO
                        ,NF.NF_VL_FRETE								AS FRETE
                        ,0                                          AS FRETE_DAV
                        ,NF.NF_NUMERO								AS ID
                        ,'VENDA SEM PEDIDO'							AS ORIGEM_COMANDO

                    FROM NF_VENDA_CABECALHO NF
                    LEFT JOIN NF_VENDA_ITENS ITENS ON ITENS.NF_NUMERO = NF.NF_NUMERO
                    LEFT JOIN CFOP ON CFOP.CFOP_CODIGO_NF = ITENS.CFOP_CODIGO_NF
                    LEFT JOIN CFOP_GRUPOS ON CFOP_GRUPOS.CFGR_CODIGO = CFOP.CFGR_CODIGO

                    LEFT JOIN dav ON dav.DAV_NUMERO = ITENS.PED_NUMERO
                    LEFT JOIN dav_itens ON dav_itens.DAV_NUMERO = dav.DAV_NUMERO
                                        AND dav_itens.DAVI_ITEM = ITENS.DAVI_ITEM_IMPORTADO

                    LEFT JOIN PAIS ON PAIS.PAIS_CODIGO = NF.PAIS_CODIGO
                    LEFT JOIN PARTICIPANTE AS REP ON REP.PART_REPRESENTANTE_CODIGO = NF.PART_REPRESENTANTE_CODIGO
                    LEFT JOIN PARTICIPANTE AS CLI ON CLI.PART_CODIGO = NF.ENT_CODIGO

                    LEFT JOIN CONDICOES_PAGAMENTO COND_PGTO ON COND_PGTO.CPA_CODIGO = NF.CON_TIPO_PAGAMENTO

                    WHERE 1=1
                    AND (CFOP_GRUPOS.CFGR_CODIGO = 1 OR RIGHT(trim(ITENS.CFOP_CODIGO),3) = '124')
                    AND NF_SITUACAO IN(1)
                    AND dav.DAV_NUMERO IS NULL
                    AND coalesce(DAV.DAV_SITUACAO_ENTREGA,0) <> 1
                    AND extract(year FROM NF_DT_EMISSAO) = $ano
                    AND extract(month FROM NF_DT_EMISSAO) = $mes


                /*******************VENDA COM DIFERENCA NA QUANTIDADE*****************************************************/
                    UNION ALL
                    SELECT
                        'SISTEMA' 									AS ORIGEM
                        ,'VENDA' 									AS TIPO
                        ,'NF' 								        AS TIPO_RECEITA
                        ,'DECORBRAS' 			   					AS EMPRESA
                        ,NF.NF_NUMERO_REAL	   		   				AS DOCUMENTO
                        ,NF.NS_SERIE			   					AS SERIE_FAT
                        ,NF.NF_DT_EMISSAO 		   	 				AS DATA_COMP
                        ,DAV.DAV_NUMERO_AUXILIAR					AS NUM_PED
                        ,DAV.DAV_DATA		   						AS DATA_PED
                        ,NF.ENT_CODIGO 				   				AS COD_CLIENTE
                        ,NF.ENT_NOME 			   	   				AS CLIENTE
                        ,NF.NF_EMISSAO_CEP         					AS CEP_IBGE
                        ,CLI.PART_ENDERECO_LOGRADOURO				AS ENDERECO
                        ,CLI.PART_ENDERECO_BAIRRO					AS BAIRRO
                        ,NF_EMISSAO_CIDADE		   	   				AS MUNICIPIO
                        ,NF_EMISSAO_UF			   	   				AS ESTADO
                        ,PAIS.PAIS_NOME				  				AS PAIS
                        ,NF.PART_REPRESENTANTE_CODIGO  				AS COD_VENDEDOR
                        ,REP.PART_NOME								AS VENDEDOR
                        ,COND_PGTO.CPA_DESCRICAO					AS FORMA_DE_PAGTO
                        ,ITENS.PRD_CODIGO							AS COD_PRODUTO
                        ,ITENS.NI_DESCRICAO			  	 			AS PRODUTO
                        ,NI_UNIMED 									AS UN_MEDIDA
                        ,DAVI_QTDE									AS QUANTIDADE
                        ,NI_VL_UNITARIO
                        ,DAVI_VL_UNIT
                        ,ITENS.NI_VL_TOTAL 				 			AS FAT_BRUTO
                        ,(DAVI_QTDE - ITENS.NI_QTDE)*NI_VL_UNITARIO AS FAT_BRUTO_2
                        ,ITENS.NI_VL_DESCONTO			 			AS DESCONTO
                        , 0 										AS ACRESCIMO
                        ,ITENS.NI_VL_TOTAL-ITENS.NI_VL_DESCONTO  	AS FAT_LIQUIDO
                        ,CFOP.CFOP_CODIGO							AS CFOP
                        ,ITENS.NI_VL_IPI				 			AS IPI
                        ,ITENS.NI_VL_PIS							AS PIS
                        ,ITENS.NI_VL_COFINS							AS COFINS
                        ,ITENS.NI_VL_ICMS               			AS ICMS
                        ,NF.NF_VL_CSLL								AS CSLL
                        ,0											AS IRPJ
                        ,NF.NF_VL_ISS 								AS ISS
                        ,0 											AS PERC_COMISSAO
                        ,ITENS.NI_COMISSAO              			AS COMISSAO
                        ,NF.NF_VL_FRETE								AS FRETE
                        ,(ITENS.NI_VL_TOTAL/NF.NF_VL_PRODUTOS) * DAV.DAV_VALOR_FRETE  AS FRETE_DAV
                        ,NF.NF_NUMERO								AS ID
                        ,'VENDA COM DIFERENCA NA QUANTIDADE'		AS ORIGEM_COMANDO

                    FROM NF_VENDA_CABECALHO NF
                    LEFT JOIN NF_VENDA_ITENS ITENS ON ITENS.NF_NUMERO = NF.NF_NUMERO
                    LEFT JOIN CFOP ON CFOP.CFOP_CODIGO_NF = ITENS.CFOP_CODIGO_NF
                    LEFT JOIN CFOP_GRUPOS ON CFOP_GRUPOS.CFGR_CODIGO = CFOP.CFGR_CODIGO

                    LEFT JOIN dav ON dav.DAV_NUMERO = ITENS.PED_NUMERO
                    LEFT JOIN dav_itens ON dav_itens.DAV_NUMERO = dav.DAV_NUMERO
                                        AND dav_itens.DAVI_ITEM = ITENS.DAVI_ITEM_IMPORTADO

                    LEFT JOIN PAIS ON PAIS.PAIS_CODIGO = NF.PAIS_CODIGO
                    LEFT JOIN PARTICIPANTE AS REP ON REP.PART_REPRESENTANTE_CODIGO = NF.PART_REPRESENTANTE_CODIGO
                    LEFT JOIN PARTICIPANTE AS CLI ON CLI.PART_CODIGO = NF.ENT_CODIGO

                    LEFT JOIN CONDICOES_PAGAMENTO COND_PGTO ON COND_PGTO.CPA_CODIGO = NF.CON_TIPO_PAGAMENTO

                    WHERE 1=1
                    AND (CFOP_GRUPOS.CFGR_CODIGO = 1 OR RIGHT(trim(ITENS.CFOP_CODIGO),3) = '124')
                    AND NF_SITUACAO IN(1)
                    AND dav.DAV_NUMERO IS NOT NULL
                    AND coalesce(DAV.DAV_SITUACAO_ENTREGA,1) =  1
                    AND extract(year FROM NF_DT_EMISSAO) = $ano
                    AND extract(month FROM NF_DT_EMISSAO) = $mes


                    UNION ALL
                    SELECT
                        'SISTEMA'          								AS ORIGEM
                        ,'VENDA'          								AS TIPO
                        ,'NF'                 						    AS TIPO_RECEITA
                        ,'DECORBRAS'            						AS EMPRESA
                        ,NF_NUMERO_REAL   								AS DOCUMENTO
                        ,NS_SERIE           							AS SERIE_FAT
                        ,NF_DT_EMISSAO           						AS DATA_COMP
                        ,DAV.DAV_NUMERO_AUXILIAR    					AS NUM_PED
                        ,DAV.DAV_DATA           						AS DATA_PED
                        ,DADOS_NOTA.ENT_CODIGO            				AS COD_CLIENTE
                        ,DADOS_NOTA.ENT_NOME               				AS CLIENTE
                        ,NF_EMISSAO_CEP          						AS CEP_IBGE
                        ,CLI.PART_ENDERECO_LOGRADOURO					AS ENDERECO
                        ,CLI.PART_ENDERECO_BAIRRO						AS BAIRRO
                        ,NF_EMISSAO_CIDADE          					AS MUNICIPIO
                        ,NF_EMISSAO_UF              					AS ESTADO
                        ,PAIS_NOME          							AS PAIS
                        ,DADOS_NOTA.PART_REPRESENTANTE_CODIGO   		AS COD_VENDEDOR
                        ,DADOS_NOTA.PART_NOME        					AS VENDEDOR
                        ,CPA_DESCRICAO     								AS FORMA_DE_PAGTO
                        ,DAV_ITENS.PRD_CODIGO       					AS COD_PRODUTO
                        ,DAVI_DESCRICAO          						AS PRODUTO
                        ,DAVI_UM          								AS UN_MEDIDA
                        ,DAVI_QTDE         								AS QUANTIDADE
                        ,DAVI_VL_UNIT									AS NI_VL_UNITARIO
                        ,DAVI_VL_UNIT
                        ,0         										AS FAT_BRUTO
                        ,DAVI_QTDE*DAVI_VL_UNIT 						AS FAT_BRUTO_2
                        ,0       										AS DESCONTO
                        , 0           									AS ACRESCIMO
                        ,DAVI_QTDE*DAVI_VL_UNIT   						AS FAT_LIQUIDO
                        ,''												AS CFOP
                        ,0				        						AS IPI
                        ,0												AS PIS
                        ,0					       						AS COFINS
                        ,0				                  				AS ICMS
                        ,0			        							AS CSLL
                        ,0           									AS IRPJ
                        ,0			         							AS ISS
                        ,0            									AS PERC_COMISSAO
                        ,DAVI_COMISSAO_PERC                 			AS COMISSAO
                        ,0        										AS FRETE
                        ,((DAVI_QTDE*DAVI_VL_UNIT)/DAV.DAV_VALOR) * DAV.DAV_VALOR_FRETE  AS FRETE_DAV
                        ,DADOS_NOTA.NF_NUMERO							AS ID
                        ,'SEM DESCRICAO'								AS ORIGEM_COMANDO

                    FROM(
                        SELECT
                            DAV.DAV_NUMERO
                            ,NF.NF_NUMERO
                            ,NF.NF_NUMERO_REAL
                            ,NF.NS_SERIE
                            ,NF.NF_DT_EMISSAO
                            ,NF.NF_EMISSAO_CEP
                            ,NF.NF_EMISSAO_CIDADE
                            ,NF.NF_EMISSAO_UF
                            ,NF.ENT_CODIGO
                            ,NF.ENT_NOME
                            ,PAIS.PAIS_NOME
                            ,NF.PART_REPRESENTANTE_CODIGO
                            ,REP.PART_NOME
                            ,COND_PGTO.CPA_DESCRICAO


                        FROM NF_VENDA_CABECALHO NF
                        LEFT JOIN NF_VENDA_ITENS ITENS ON ITENS.NF_NUMERO = NF.NF_NUMERO
                        LEFT JOIN CFOP ON CFOP.CFOP_CODIGO_NF = ITENS.CFOP_CODIGO_NF
                        LEFT JOIN CFOP_GRUPOS ON CFOP_GRUPOS.CFGR_CODIGO = CFOP.CFGR_CODIGO

                        LEFT JOIN DAV ON DAV.DAV_NUMERO = ITENS.PED_NUMERO
                        LEFT JOIN DAV_ITENS ON DAV_ITENS.DAV_NUMERO = dav.DAV_NUMERO
                                            AND DAV_ITENS.DAVI_ITEM = ITENS.DAVI_ITEM_IMPORTADO
                        LEFT JOIN PAIS ON PAIS.PAIS_CODIGO = NF.PAIS_CODIGO
                        LEFT JOIN PARTICIPANTE AS REP ON REP.PART_REPRESENTANTE_CODIGO = NF.PART_REPRESENTANTE_CODIGO

                        LEFT JOIN CONDICOES_PAGAMENTO COND_PGTO ON COND_PGTO.CPA_CODIGO = NF.CON_TIPO_PAGAMENTO

                        WHERE 1=1
                        AND (CFOP_GRUPOS.CFGR_CODIGO = 1 OR RIGHT(trim(ITENS.CFOP_CODIGO),3) = '124')
                        AND NF_SITUACAO IN(1)
                        AND coalesce(DAV.DAV_SITUACAO_ENTREGA,1) =  1
                        AND dav.DAV_NUMERO IS NOT NULL
                        AND extract(year FROM NF_DT_EMISSAO) = $ano
                        AND extract(month FROM NF_DT_EMISSAO) = $mes

                        GROUP BY
                            DAV.DAV_NUMERO
                            ,NF.NF_NUMERO
                            ,NF.NF_NUMERO_REAL
                            ,NF.NS_SERIE
                            ,NF.NF_DT_EMISSAO
                            ,NF.NF_EMISSAO_CEP
                            ,NF.NF_EMISSAO_CIDADE
                            ,NF_EMISSAO_UF
                            ,NF.ENT_CODIGO
                            ,NF.ENT_NOME
                            ,PAIS.PAIS_NOME
                            ,NF.PART_REPRESENTANTE_CODIGO
                            ,REP.PART_NOME
                            ,COND_PGTO.CPA_DESCRICAO

                    )DADOS_NOTA
                    LEFT JOIN DAV ON DAV.DAV_NUMERO = DADOS_NOTA.DAV_NUMERO
                    LEFT JOIN DAV_ITENS ON DAV_ITENS.DAV_NUMERO = DAV.DAV_NUMERO
                                        AND DAV_ITENS.DAVI_QTDE_ENTREGA = 0
                    LEFT JOIN PARTICIPANTE AS CLI ON CLI.PART_CODIGO = DADOS_NOTA.ENT_CODIGO


                /*******************ROMANEIO*****************************************************************************/
                    UNION ALL
                    Select
                        'SISTEMA' 						 		    AS ORIGEM
                        ,'VENDA' 						 		    AS TIPO
                        ,DAV.DAV_TIPO 					  	        AS TIPO_RECEITA
                        ,'DECORBRAS' 			   				    AS EMPRESA
                        ,DAV.DAV_NUMERO_AUXILIAR	   	   	   	    AS DOCUMENTO
                        ,''	   		   				   			    AS SERIE_FAT
                        ,DAV.DAV_DATA_EMISSAO		   	 	   	    AS DATA_COMP
                        ,DAV.DAV_NUMERO_AUXILIAR				    AS NUM_PED
                        ,DAV.DAV_DATA_EMISSAO					    AS DATA_PED
                        ,DAV.ENT_CODIGO		   					    AS COD_CLIENTE
                        ,DAV_NOME_ADQUIRENTE	   	  			    AS CLIENTE
                        ,DAV_ENDERECO_CEP   				   	    AS CEP_IBGE
                        ,CLI.PART_ENDERECO_LOGRADOURO				AS ENDERECO
                        ,CLI.PART_ENDERECO_BAIRRO					AS BAIRRO
                        ,MUNICIPIO.MUN_NOME					   	    AS MUNICIPIO
                        ,MUNICIPIO.UF_CODIGO			            AS ESTADO
                        ,''							  			    AS PAIS
                        ,DAV.PART_REPRESENTANTE_CODIGO			    AS COD_VENDEDOR
                        ,REP.PART_NOME	                            AS VENDEDOR
                        ,CONDICOES_PAGAMENTO.CPA_DESCRICAO		    AS FORMA_DE_PAGTO
                        ,DAV_ITENS.PRD_CODIGO					    AS COD_PRODUTO
                        ,DAV_ITENS.DAVI_DESCRICAO  				    AS PRODUTO
                        ,DAV_ITENS.DAVI_UM						    AS UN_MEDIDA
                        ,DAV_ITENS.DAVI_QTDE  			  		    AS QUANTIDADE
                        ,0								    	    AS NI_VL_UNITARIO
                        ,DAVI_VL_UNIT
                        ,0								 		    AS FAT_BRUTO
                        ,DAV_ITENS.DAVI_VL_TOTAL		  	        AS FAT_BRUTO_2
                        ,DAV_ITENS.DAVI_VL_DESCONTO		 	   	    AS DESCONTO
                        ,DAV_ITENS.DAVI_VL_ACRESCIMO			    AS ACRESCIMO
                        ,DAV_ITENS.DAVI_VL_TOTAL                    AS FAT_LIQUIDO
                        ,''											AS CFOP
                        ,0				                            AS IPI
                        ,DAV_ITENS.DAVI_PIS_VALOR					AS PIS
                        ,DAV_ITENS.DAVI_COFINS_VALOR			    AS COFINS
                        ,DAV_ITENS.DAVI_ICMS_VALOR          	    AS ICMS
                        ,0									 	    AS CSLL
                        ,0										    AS IRPJ
                        ,0										    AS ISS
                        ,DAV_ITENS.DAVI_COMISSAO_PERC			    AS PERC_COMISSAO
                        ,DAV_ITENS.DAVI_VALOR_COMISSAO              AS COMISSAO
                        ,DAV_ITENS.DAVI_FRETE_VALOR_TOTAL		    AS FRETE
                        ,((DAVI_QTDE*DAVI_VL_UNIT)/DAV.DAV_VALOR) * DAV.DAV_VALOR_FRETE  AS FRETE_DAV
                        ,DAV_NUMERO_AUXILIAR						AS ID
                        ,'ROMANEIO'									AS ORIGEM_COMANDO


                    FROM DAV
                    LEFT JOIN DAV_ITENS		ON DAV_ITENS.DAV_NUMERO = DAV.DAV_NUMERO
                    LEFT JOIN MUNICIPIO		ON MUNICIPIO.MUN_CODIGO = DAV.MUN_CODIGO
                    LEFT JOIN PARTICIPANTE REP	ON REP.PART_REPRESENTANTE_CODIGO = DAV.PART_REPRESENTANTE_CODIGO
                    LEFT JOIN CONDICOES_PAGAMENTO ON CONDICOES_PAGAMENTO.CPA_CODIGO = DAV.CON_TIPO_PAGAMENTO
                    LEFT JOIN PARTICIPANTE AS CLI ON CLI.PART_CODIGO = DAV.ENT_CODIGO


                    WHERE 1=1
                    AND DSP_CODIGO = 1
                    AND extract(year FROM DAV_DATA_EMISSAO) = $ano
                    AND extract(month FROM DAV_DATA_EMISSAO) = $mes

            )vendas
            LEFT JOIN PRODUTOS ON PRODUTOS.PRD_CODIGO = vendas.COD_PRODUTO
            LEFT JOIN PRODUTOS_GRUPOS AS GRP ON GRP.GRP_CODIGO = PRODUTOS.GRP_CODIGO

            where 1 = 1
            AND COD_PRODUTO IS NOT NULL
            AND PRODUTOS.PRD_TIPO_PRODUTO IN ('04')
            --AND PRODUTOS.PRD_IPPT IN ('P')

            GROUP BY COD_PRODUTO,PRODUTO
        ";
        $produto = db::connection(env('APP_NAME'))->select($sql);
        produto_ficha::truncate();

        foreach($produto as $item){
            $ficha = Ficha::estrutura($item->COD_PRODUTO);
            ($ficha)? $imprimir='N' : $imprimir='S';
            if($imprimir=='S'||$mostrar=='S'){
                print_r('<b><i>'.$item->COD_PRODUTO.' - '.$item->PRODUTO.'</i></b><p>');
            };
            $table = '<table width="100%">';
            foreach($ficha as $item_ficha){
                try{
                    $ficha_mysql = new produto_ficha([
                        'cod'            => $item_ficha->COD
                        , 'descr'        => $item_ficha->DESCR
                        , 'grupo'        => $item_ficha->GRUPO
                        , 'cod_pai'      => $item_ficha->COD_PAI
                        , 'desc_pai'     => $item_ficha->DESC_PAI
                        , 'cod_comp'     => $item_ficha->COD_COMP
                        , 'desc_comp'    => $item_ficha->DESC_COMP
                        , 'tipo_comp'    => $item_ficha->TIPO_COMP
                        , 'grupo_comp'   => $item_ficha->GRUPO_COMP
                        , 'qtde_calc'    => $item_ficha->QTDE_CALC
                        , 'nivel'        => $item_ficha->NIVEL
                    ]);
                    $ficha_mysql->save();
                }catch(\Exception $e){

                }
                $table .= '<tr>';
                    $table .= '<td>'.$item_ficha->COD_COMP.'</td>';
                    $table .= '<td>'.$item_ficha->DESC_COMP.'</td>';
                    $table .= '<td aling="right">'.number_format($item_ficha->QTDE_CALC,2,',','.').'</td>';
                $table .= '</tr>';
                // print_r($item_ficha->COD_COMP.''.$item_ficha->DESC_COMP.'<br>');
            }
            $table .= '</table>';
            if($imprimir=='S'||$mostrar=='S'){
                print_r($table);
                print_r('<hr>'.'<br>');
            }
        }
    }

    public function produtoComFicha(Request $request)
    {
        return view('fechamento.produtoComFicha');
    }

    public function consutaProdutoComFicha(Request $request)
    {
        $pro_descr = strtoupper($request->pro_descr);
        $pro_descr = str_replace(' ','%',$pro_descr);
        $sql = "
            SELECT
                *
            FROM(
                SELECT
                    PRODUTOS.PRD_CODIGO
                    ,PRODUTOS.PRD_DESCRICAO
                    ,COUNT(PRODUTOS_COMPONENTES_PRODUCAO.PRCP_ITEM) QT_COMP
                FROM PRODUTOS
                LEFT JOIN PRODUTOS_COMPONENTES_PRODUCAO ON PRODUTOS_COMPONENTES_PRODUCAO.PRD_CODIGO = PRODUTOS.PRD_CODIGO
                WHERE PRODUTOS.PRD_TIPO_PRODUTO IN ('04')
                GROUP BY
                    PRODUTOS.PRD_CODIGO
                    ,PRODUTOS.PRD_DESCRICAO
            )DADOS_FICHA
            WHERE DADOS_FICHA.QT_COMP>0
            AND PRD_DESCRICAO LIKE UPPER('%$pro_descr%')
            ORDER BY PRD_DESCRICAO
        ";
        $produto = db::connection(env('APP_NAME'))->select($sql);
        return $produto;
    }

    public function importaHorasMlc()
    {
        return view('fechamento.importaHorasMlc');
    }

    public function gravaHorasMLC(Request $request)
    {
        $data = $request->texto;
        $texto = explode("\n",$data);
        $retorno = '';
        foreach($texto as $linhas){
            $linha          = explode("\t",$linhas);
            $ano            = intval(trim($linha[0]));
            $mes            = intval(trim($linha[1]));
            $CodCa          = intval(trim($linha[2]));
            $valor          = floatval(str_replace(',','.',str_replace('.','',$linha[3])));
            $CodConta       = intval(trim($linha[4]));

            $Existehora = mlc_hora::where('Ano',$ano)->where('CodConta',$CodConta)->where('CodCa',$CodCa)->count();
            $Existehoraprod = mlc_horaprod::where('Ano',$ano)->where('CodCa',$CodCa)->count();

            if($Existehora==0){
                Mlc::horasInsert($ano,$mes,$CodCa,$CodConta,$valor);
            }else{
                Mlc::horasUpd($ano,$mes,$CodCa,$CodConta,$valor);
            }

            if($Existehoraprod==0){
                Mlc::horasProdInsert($ano,$mes,$CodCa,$CodConta,$valor);
            }else{
                Mlc::horasProdUpd($ano,$mes,$CodCa,$CodConta,$valor);
            }

        }
        return response()->json(($retorno));
    }


}
