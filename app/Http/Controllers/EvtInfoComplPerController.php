<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtInfoComplPerController extends Controller{

    // S-1280
    public function evtInfComp(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1; //Opcional
        $std->indretif = 1; //Obrigatório
        $std->nrrecibo = null; //Obrigatório apenas se indretif = 2
        $std->indapuracao = 1; //Obrigatório
        $std->perapur = '2017-08'; //Obrigatório
        $std->indguia = 1; //Opcional

        //Grupo preenchido exclusivamente por empresa enquadrada nos arts. 7o a 9o da Lei 12.546/2011,
        // conforme classificação tributária indicada no evento S-1000.
        $std->infosubstpatr = new \stdClass(); //Opcional
        $std->infosubstpatr->indsubstpatr = 1; //Obrigatório
        $std->infosubstpatr->percpedcontrib = 2.50; //Obrigatório

        //Grupo preenchido exclusivamente pelo Órgão Gestor de Mão de Obra - OGMO (classTrib em S-1000 = [09]),
        //listando apenas seus códigos de lotação com operadores portuários enquadrados nos arts. 7o a 9o
        //da Lei 12.546/2011.
        $std->infosubstpatropport[0] = new \stdClass(); //Opcional
        $std->infosubstpatropport[0]->codlotacao = '11111111111111'; //Obrigatório

        //Grupo preenchido por empresa enquadrada no regime de tributação Simples Nacional com tributação
        //previdenciária substituída e não substituída.
        $std->infoativconcom = new \stdClass();  //Opcional
        $std->infoativconcom->fatormes = 1.11; //Obrigatório
        $std->infoativconcom->fator13 = 0.22; //Obrigatório

        $std->infoperctransf11096 = new \stdClass();  //Opcional
        //Transformação de entidade beneficente em empresa de fins lucrativos
        //DESCRICAO_COMPLETA:Grupo preenchido por entidade que tenha se transformado em sociedade de fins lucrativos nos
        // termos e no prazo da Lei 11.096/2005
        // CONDICAO_GRUPO: O (se {dtTrans11096}(1000_infoEmpregador_inclusao_infoCadastro_dtTrans11096) em S-1000 for informado);
        // N (nos demais casos)</xs:documentation>
        $std->infoperctransf11096->perctransf = 1; //Obrigatório
        //Informe o percentual de contribuição social devida em caso de transformação em sociedade de
        // fins lucrativos - Lei 11.096/2005.
        // 1 - 0,2000
        // 2 - 0,4000
        // 3 - 0,6000
        // 4 - 0,8000
        // 5 - 1,0000


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtInfoComplPer(
                $configJson,
                $std,
                $certificate,
                '2017-08-03 10:37:00' //opcional data e hora
            )->toXml();

            header('Content-type: text/xml; charset=UTF-8');
            echo $xml;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}