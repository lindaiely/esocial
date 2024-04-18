<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtCdBenAltController extends Controller{

    // S-2416
    public function evtCdBenAltC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 1; //obrigatorio
        $std->nrrecibo = '1.4.1234567890123456789'; //opcional
        $std->cpfbenef = '12345678901'; //obrigatorio
        $std->nrbeneficio = 'b1234'; //obrigatorio
        $std->dtaltbeneficio = '2021-03-02';
        $std->tpbeneficio = "0805";
        $std->tpplanrp = 0;
        $std->dsc = "bla bla bla bla";
        $std->indsuspensao = "N";
        //opcional
        $std->infopenmorte = new \stdClass();
        $std->infopenmorte->tppenmorte = 1; //obrigatorio
        //opcional
        $std->suspensao = new \stdClass();
        $std->suspensao->mtvsuspensao = '01';
        $std->suspensao->dscsuspensao = 'bla bla bla bla';


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtCdBenAlt(
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