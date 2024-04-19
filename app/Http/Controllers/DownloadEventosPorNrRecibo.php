<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;
use NFePHP\eSocial\Tools;

class DownloadEventosPorNrRecibo extends Controller{

    public function downloadPorRecibo(Request $request){

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
            //A = Agente de processamento: Serpro=1
            //B = Ambiente de recepção:
            //     1=Produção;
            //     2=Pré-produção - dados reais;
            //     3=Pré-produção - dados fictícios;
            //     6=Homologação;
            //     7=Validação;
            //     8=Testes;
            //     9=Desenvolvimento;
            //N = Número sequencial (19 posições)
            //nrRecibo = A.B.N
            $nrRec = [
                '1.1.1234567890123456789',
                '1.1.1234567890123456788',
                '1.1.1234567890123456787'
            ];
        
            $response = $tools->downloadEventosPorNrRecibo($nrRec);
        
            header('Content-Type: application/xml; charset=utf-8');
            echo $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}