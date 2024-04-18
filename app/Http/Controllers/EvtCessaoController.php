<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtCessaoController extends Controller{

    // S-2231
    public function evtCessaoC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1; //Opcional
        $std->indretif = 1; //Obrigatório
        $std->nrrecibo = '1.1.1234567890123456789'; //Obrigatório APENAS se indretif = 2

        $std->cpftrab = '11111111111'; //Obrigatório
        $std->matricula = '11111111111'; //Obrigatório

        //Informações da cessão/exercício em outro órgão
        $std->inicessao = new \stdClass(); //Opcional
        $std->inicessao->dtinicessao = '2017-08-21'; //Obrigatório
        $std->inicessao->cnpjcess = '12345678901234'; //Obrigatório
        $std->inicessao->respremun = 'N'; //Obrigatório

        //Informação de término da cessão/exercício em outro órgão.
        $std->fimcessao = new \stdClass(); //Opcional
        $std->fimcessao->dttermcessao = '2019-08-21'; //Obrigatório

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtCessao(
                $configJson,
                $std,
                $certificate
            )->toXml();

            header('Content-type: text/xml; charset=UTF-8');
            echo $xml;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}