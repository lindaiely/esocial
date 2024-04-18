<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtTabRubricaController extends Controller{

    // S-1010
    public function evtTabRub(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1; //Opcional
        $std->codrubr = 'alalalaallkj r9487dkjhdkjhd';
        $std->idetabrubr = 'lslslsls';
        $std->inivalid = '2017-01';
        $std->fimvalid = '2017-12';
        $std->modo = "INC";

        //campo obirgatório
        $std->dadosrubrica  = new \stdClass();
        $std->dadosrubrica->dscrubr = 'dkdldkdlk';
        $std->dadosrubrica->natrubr = 1234;
        $std->dadosrubrica->tprubr = 1;
        $std->dadosrubrica->codinccp = '11';
        $std->dadosrubrica->codincirrf = '11';
        $std->dadosrubrica->codincfgts = '11';
        $std->dadosrubrica->codinccprp = '11';
        $std->dadosrubrica->tetoremun = 'N';
        $std->dadosrubrica->observacao = null;

        //campo ARRAY opcional
        $std->dadosrubrica->ideprocessocp[0] = new \stdClass();
        $std->dadosrubrica->ideprocessocp[0]->tpproc = 1;
        $std->dadosrubrica->ideprocessocp[0]->nrproc = '12345678901234567';
        $std->dadosrubrica->ideprocessocp[0]->extdecisao = 1;
        $std->dadosrubrica->ideprocessocp[0]->codsusp = '0929292882';

        //campo ARRAY opcional
        $std->dadosrubrica->ideprocessoirrf[0] = new \stdClass();
        $std->dadosrubrica->ideprocessoirrf[0]->nrproc  = 'asdfghjkliuytrewqasd';
        $std->dadosrubrica->ideprocessoirrf[0]->codsusp = '0929292882';

        //campo ARRAY opcional
        $std->dadosrubrica->ideprocessofgts[0] = new \stdClass();
        $std->dadosrubrica->ideprocessofgts[0]->nrproc  = 'asdfghjkliuytrewqasd';


        //campos opcionais usar apenas em alterações
        $std->novavalidade = new \stdClass();
        $std->novavalidade->inivalid = '2017-12';
        $std->novavalidade->fimvalid = '2018-12';

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtTabRubrica(
                $configJson,
                $std,
                $certificate,
                '2017-08-03 10:37:00' //opcional data e hora
            )->toXml();

            //$xml = Evento::s1010($json, $std, $certificate)->toXML();
            //$json = Event::evtTabRubrica($configjson, $std, $certificate)->toJson();

            header('Content-type: text/xml; charset=UTF-8');
            echo $xml;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}