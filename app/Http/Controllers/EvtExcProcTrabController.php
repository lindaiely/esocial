<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtExcProcTrabController extends Controller{

    // S-3500
    public function evtExcProc(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;

        $std->infoexclusao = new \stdClass();
        $std->infoexclusao->tpevento = 'S-1200';
        $std->infoexclusao->nrrecevt = '1.9.1234567890123456789';

        $std->ideproctrab = new \stdClass();
        $std->ideproctrab->nrproctrab = '123456789012345';
        $std->ideproctrab->cpftrab = '11111111111';
        $std->ideproctrab->perapurpgto = '2023-01';


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtExcProcTrab(
                $configJson,
                $std,
                $certificate,
                '2017-08-03 10:37:00'
            )->toXml();

            header('Content-type: text/xml; charset=UTF-8');
            echo $xml;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}