<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtTabProcessoController extends Controller{

    // S-1070
    public function evtTabP(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1; //Opcional
        $std->tpproc = 1;
        $std->nrproc = '1234567890123456';
        $std->inivalid = '2017-01';
        $std->fimvalid = '2017-12';
        $std->modo = 'EXC';

        $std->dadosproc = new \stdClass();
        $std->dadosproc->indautoria = 1;
        $std->dadosproc->indmatproc = 7;
        $std->dadosproc->observacao = 'lalsksksksksk';

        $std->dadosproc->dadosprocjud = new \stdClass();
        $std->dadosproc->dadosprocjud->ufvara = 'SP';
        $std->dadosproc->dadosprocjud->codmunic = '3550308';
        $std->dadosproc->dadosprocjud->idvara = '234';

        $std->dadosproc->infosusp[0] = new \stdClass();
        $std->dadosproc->infosusp[0]->codsusp = '12345678901234';
        $std->dadosproc->infosusp[0]->indsusp = '01';
        $std->dadosproc->infosusp[0]->dtdecisao = '2017-07-22';
        $std->dadosproc->infosusp[0]->inddeposito = 'N';

        $std->novavalidade = new \stdClass();
        $std->novavalidade->inivalid = '2017-12';
        $std->novavalidade->fimvalid = '2018-12';

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtTabProcesso(
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