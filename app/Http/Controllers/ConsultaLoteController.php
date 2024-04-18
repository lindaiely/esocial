<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;
use NFePHP\eSocial\Common\Soap\SoapFake;
use NFePHP\eSocial\Common\FakePretty;
use NFePHP\eSocial\Tools;


class ConsultaLoteController extends Controller{

    public function consultaLote(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);
        
            //usar a classe Fake para não tentar enviar apenas ver o resultado da chamada
            $soap = new SoapFake();
            //desativa a validação da validade do certificado
            //estamos usando um certificado vencido nesse teste
            //$soap->disableCertValidation(true);
        
            //instancia a classe responsável pela comunicação
            $tools = new Tools($configJson, $certificate);
            //carrega a classe responsável pelo envio SOAP
            //nesse caso um envio falso
            $tools->loadSoapClass($soap);
        
            //executa a consulta
            $response = $tools->consultarLoteEventos('1.2.201707.0000000000000007638');
        
            //retorna os dados que serão usados na conexão para conferência
            echo FakePretty::prettyPrint($response, '');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}