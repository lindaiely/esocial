<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtReativBenController extends Controller{

    // S-2418
    public function evtReativBenC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 1; //obrigatorio
        $std->nrrecibo = '1.4.1234567890123456789'; //opcional
        $std->cpfbenef = '12345678901'; //obrigatorio
        $std->nrbeneficio = 'b1234'; //obrigatorio
        $std->dtefetreativ = '2021-05-20'; //obrigatorio
        $std->dtefeito = '2021-06-01'; //obrigatorio


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtReativBen(
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