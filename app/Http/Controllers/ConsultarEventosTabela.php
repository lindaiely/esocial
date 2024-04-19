<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;
use NFePHP\eSocial\Tools;

class ConsultarEventosTabela extends Controller{

    public function consultaEventosTab(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);
        
            //instancia a classe responsável pela comunicação
            $tools = new Tools($configJson, $certificate);
        
            //Tipo do evento  | Chave(s) do evento   | Exemplo
            //S-1000          | -                    | vazio
            //S-1005          | tpInsc e nrInsc      | tpInsc=1;nrInsc=11223344556677...
            //S-1010          | codRubr e ideTabRubr | codRubr=1;ideTabRubr=1
            //S-1020          | codLotacao           | codLotacao=001
            //S-1030          | codCargo             | codCargo=001
            //S-1035          | CodCarreira          | CodCarreira=001
            //S-1040          | codFuncao            | codFuncao=001
            //S-1050          | codHorContrat        | codHorContrat=001
            //S-1060          | codAmb               | codAmb=001
            //S-1065          | codEP                | codEP=001
            //S-1070          | tpProc e nrProc      | tpProc=1;nrProc=12345678...
            //S-1080          | cnpjOpPortuario      | cnpjOpPortuario=111222333...
        
            //executa a consulta
            $tpEvt = 'S-1080';//obrigatório
            $chEvt = 'cnpjOpPortuario=11122233344'; //opcional
            $dtIni = '2012-12-13T12:12:12'; //opcional
            $dtFim = '2012-12-13T12:12:12'; //opcional
        
            $response = $tools->consultarEventosTabela($tpEvt, $chEvt, $dtIni, $dtFim);
        
            header('Content-Type: application/xml; charset=utf-8');
            echo $response;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}