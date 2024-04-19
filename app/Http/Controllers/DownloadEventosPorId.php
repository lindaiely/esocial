<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;
use NFePHP\eSocial\Tools;

class DownloadEventosPorId extends Controller{

    public function downloadPorId(Request $request){

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
            $id = [
                'ID1999999990000002017080310370000001',
                'ID1999999990000002017080310370000002',
                'ID1999999990000002017080310370000003',
            ];
        
            $response = $tools->downloadEventosPorId($id);
        
            header('Content-Type: application/xml; charset=utf-8');
            echo $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}