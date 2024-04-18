<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtComProdController extends Controller{

    // S-1260
    public function evtComProdC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1; //Opcional
        $std->indretif = 1; //Obrigatório
        $std->nrrecibo = "1.7.1234567890123456789"; //Obrigatório caso indretif = 2
        $std->perapur = '2017-08'; //Obrigatório
        $std->indguia = 1; //Opcional

        //Identificação do estabelecimento que comercializou a produção.
        $std->estabelecimento = new \stdClass(); //Obrigatório
        $std->estabelecimento->nrinscestabrural = '12345678901234'; //Obrigatório

        //Valor total da comercialização por "tipo" de comercialização
        $std->estabelecimento->tpcomerc[0] = new \stdClass(); //Obrigatório
        $std->estabelecimento->tpcomerc[0]->indcomerc = '2'; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->vrtotcom = 1500.00; //Obrigatório

        //Identificação dos adquirentes da produção.
        $std->estabelecimento->tpcomerc[0]->ideadquir[0] = new \stdClass(); //Opcional
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->tpinsc = '1'; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nrinsc = '12345678901234'; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->vrcomerc = 1500.22; //Obrigatório

        //Detalhamento das notas fiscais relativas à comercialização
        //de produção com o adquirente identificado no grupo superior.
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nfs[0] = new \stdClass(); //Opcional
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nfs[0]->serie = '12345'; //Opcional
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nfs[0]->nrdocto = '1111111111111111111'; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nfs[0]->dtemisnf = '2017-08-23'; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nfs[0]->vlrbruto = 1500.44; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nfs[0]->vrcpdescpr = 1500.55; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nfs[0]->vrratdescpr = 1500.66; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->ideadquir[0]->nfs[0]->vrsenardesc = 1500.77; //Obrigatório

        //Informações de processos judiciais com decisão/sentença
        //favorável ao contribuinte e relativos à contribuição
        //incidente sobre a comercialização.
        $std->estabelecimento->tpcomerc[0]->infoprocjud[0] = new \stdClass(); //Opcional
        $std->estabelecimento->tpcomerc[0]->infoprocjud[0]->tpproc = 1; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->infoprocjud[0]->nrproc = '12345678901234567'; //Obrigatório
        $std->estabelecimento->tpcomerc[0]->infoprocjud[0]->codsusp = '11111111111111'; //Obrigatório

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtComProd(
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