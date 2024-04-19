<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;
use NFePHP\eSocial\Tools;

class ConsultarEventosTrabalhador extends Controller{

    public function consultaEventosTrab(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);
        
            //instancia a classe responsável pela comunicação
            $tools = new Tools($configJson, $certificate);
        
            //executa a consulta
            $cpfTrab = '12345678901';
            $dtIni = '2012-12-13T12:12:12'; //opcional
            $dtFim = '2012-12-13T12:12:12'; //opcional
        
            $response = $tools->consultarEventosTrabalhador($cpfTrab, $dtIni, $dtFim);
        
            header('Content-Type: application/xml; charset=utf-8');
            echo $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}