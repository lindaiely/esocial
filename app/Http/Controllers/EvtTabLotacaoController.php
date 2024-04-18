<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtTabLotacaoController extends Controller{

    // S-1020
    public function evtTabLot(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1; //Opcional
        $std->codlotacao = 'assistente';
        $std->inivalid = '2017-01';
        $std->fimvalid = '2017-12';
        $std->modo = 'INC';

        //campo obrigatório
        $std->dadoslotacao = new \stdClass();
        $std->dadoslotacao->tplotacao = '01';
        $std->dadoslotacao->tpinsc = 1;
        $std->dadoslotacao->nrinsc = '12345678901234';
        $std->dadoslotacao->fpas = '507';
        $std->dadoslotacao->codtercs = '0064';
        $std->dadoslotacao->codtercssusp = '0072';

        //campo opcional
        $std->dadoslotacao->procjudterceiro[0] = new \stdClass();
        $std->dadoslotacao->procjudterceiro[0]->codterc = '0064';
        $std->dadoslotacao->procjudterceiro[0]->nrprocjud = '12345678901234567890';
        $std->dadoslotacao->procjudterceiro[0]->codsusp = '1234567';

        //campo opcional
        $std->dadoslotacao->infoemprparcial = new \stdClass();
        $std->dadoslotacao->infoemprparcial->tpinsccontrat = 1;
        $std->dadoslotacao->infoemprparcial->nrinsccontrat = '12345678901234';
        $std->dadoslotacao->infoemprparcial->tpinscprop = 2;
        $std->dadoslotacao->infoemprparcial->nrinscprop = '12345678901234';

        //campo opcional
        $std->dadoslotacao->dadosopport = new \stdClass();
        $std->dadoslotacao->dadosopport->aliqrat = 3;
        $std->dadoslotacao->dadosopport->fap = 1.04;

        //campo opcional, usar somente qunado alteração
        $std->novavalidade = new \stdClass();
        $std->novavalidade->inivalid = '2017-01';
        $std->novavalidade->fimvalid = '2017-12';

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtTabLotacao(
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