<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtContratAvNPController extends Controller{

    // S-1270
    public function evtContratAvNPC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1; //Opcional
        $std->indretif = 1; //Obrigatório
        $std->nrrecibo = null; //Obrigatório apenas se inderetif = 2
        $std->perapur = '2017-08'; //Obrigatório
        $std->indguia = 1; //Opcional

        $std->remunavnp[0] = new \stdClass(); //Obrigatório
        $std->remunavnp[0]->tpinsc = "1"; //Obrigatório
        $std->remunavnp[0]->nrinsc = '11111111111111'; //Obrigatório
        $std->remunavnp[0]->codlotacao = '11111111111111'; //Obrigatório
        $std->remunavnp[0]->vrbccp00 = 1500.11; //Obrigatório
        $std->remunavnp[0]->vrbccp15 = 1500.22; //Obrigatório
        $std->remunavnp[0]->vrbccp20 = 1500.33; //Obrigatório
        $std->remunavnp[0]->vrbccp25 = 1500.44; //Obrigatório
        $std->remunavnp[0]->vrbccp13 = 1500.55; //Obrigatório
        $std->remunavnp[0]->vrbcfgts = 1500.66; //Obrigatório
        $std->remunavnp[0]->vrdesccp = 1500.77; //Obrigatório


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtContratAvNP(
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