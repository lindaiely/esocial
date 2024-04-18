<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtCdBenTermController extends Controller{

    // S-2420
    public function evtCdBenTermC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 2; //obrigatorio
        $std->nrrecibo = '1.4.1234567890123456789'; //opcional
        $std->cpfbenef = '12345678901'; //obrigatorio
        $std->nrbeneficio = 'b1234'; //obrigatorio
        $std->dttermbeneficio = '2021-10-12'; //obrigatorio
        $std->mtvtermino = '01'; //obrigatorio
        $std->cnpjorgaosuc = '12345678901234'; //opcional
        $std->novocpf = '12345678901'; //opcional


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtCdBenTerm(
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