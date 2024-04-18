<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtAfastTempController extends Controller{

    // S-2230
    public function evtAfast(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 1;
        $std->nrrecibo = '1.1.1234567890123456789';

        $std->idevinculo = new \stdClass();
        $std->idevinculo->cpftrab = '11111111111';
        $std->idevinculo->matricula = '11111111111';

        //Opcional 1 ou Opcional 2 ou Opcional 3
        $std->iniafastamento = new \stdClass();
        $std->iniafastamento->dtiniafast = '2017-08-21';
        $std->iniafastamento->codmotafast = '01';
        $std->iniafastamento->infomesmomtv = 'N';
        $std->iniafastamento->tpacidtransit = 3;
        $std->iniafastamento->observacao = 'blablablabla';

        $std->iniafastamento->peraquis = new \stdClass();
        $std->iniafastamento->peraquis->dtinicio = '2016-08-21';
        $std->iniafastamento->peraquis->dtfim = '2017-08-20';

        $std->iniafastamento->infocessao = new \stdClass();
        $std->iniafastamento->infocessao->cnpjcess = '11111111111111';
        $std->iniafastamento->infocessao->infonus = 1;

        $std->iniafastamento->infomandsind = new \stdClass();
        $std->iniafastamento->infomandsind->cnpjsind = '11111111111111';
        $std->iniafastamento->infomandsind->infonusremun = 1;

        $std->iniafastamento->infomandelet = new \stdClass();
        $std->iniafastamento->infomandelet->cnpjmandelet = '11111111111111';
        $std->iniafastamento->infomandelet->indremuncargo = 'N';

        //Opcional 2
        $std->inforetif = new \stdClass();
        $std->inforetif->origretif = 1;
        $std->inforetif->tpproc = 1;
        $std->inforetif->nrproc = '1234567890';

        //Opcional 3
        $std->fimafastamento = new \stdClass();
        $std->fimafastamento->dttermafast = '2017-08-21';

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtAfastTemp(
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