<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;
use NFePHP\eSocial\Tools;

class Envia extends Controller{

    public function envia(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);
        
            //carrega os dados do envento
            $std = new \stdClass();
            //$std->sequencial = 1; //Opcional
            $std->indretif = 1;
            $std->nrrecibo = "1.7.1234567890123456789";
            $std->cpftrab = '00232133417';
            $std->dtnascto = '1931-02-12';
            $std->dtadm = '2017-02-12';
            $std->matricula = "abs1234";
            $std->codcateg = "101";
            $std->natatividade = 1;

            $std->inforegctps = new \stdClass();
            $std->inforegctps->cbocargo = "263105";
            $std->inforegctps->vrsalfx = "2500";
            $std->inforegctps->undsalfixo = 3;
            $std->inforegctps->tpcontr = 1;
            $std->inforegctps->dtterm = null;

            $evento = Event::evtAdmPrelim($configJson, $std);
        
            //instancia a classe responsável pela comunicação
            $tools = new Tools($configJson, $certificate);
        
            //executa o envio
            $response = $tools->enviarLoteEventos($tools::EVT_INICIAIS, [$evento]);
        
            header('Content-Type: application/xml; charset=utf-8');
            echo $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}