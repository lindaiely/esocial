<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtMonitController extends Controller{

    // S-2220
    public function evtMonitC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 1;
        $std->nrrecibo = "1.7.1234567890123456789";
        $std->idevinculo = new \stdClass();
        $std->idevinculo->cpftrab = '11111111111';
        $std->idevinculo->matricula = '11111111111';
        $std->idevinculo->codcateg = '111';

        $std->exmedocup = new \stdClass();
        $std->exmedocup->tpexameocup = 1;

        $std->exmedocup->aso = new \stdClass();
        $std->exmedocup->aso->dtaso = '2017-08-18';
        $std->exmedocup->aso->resaso = 1;

        $std->exmedocup->aso->exame[0] = new \stdClass();
        $std->exmedocup->aso->exame[0]->dtexm = '2017-08-18';
        $std->exmedocup->aso->exame[0]->procrealizado = '1010';
        $std->exmedocup->aso->exame[0]->obsproc = 'observação do exame';
        $std->exmedocup->aso->exame[0]->ordexame = 1;
        $std->exmedocup->aso->exame[0]->indresult = 1;

        $std->exmedocup->aso->medico = new \stdClass();
        $std->exmedocup->aso->medico->cpfmed = '12345678901';
        $std->exmedocup->aso->medico->nismed = '12345678901';
        $std->exmedocup->aso->medico->nmmed = 'NOME DO MEDICO';
        $std->exmedocup->aso->medico->nrcrm = '12345678';
        $std->exmedocup->aso->medico->ufcrm = 'SP';

        $std->exmedocup->respmonit = new \stdClass();
        $std->exmedocup->respmonit->cpfresp = '12345678901';
        $std->exmedocup->respmonit->nmresp= 'Fulano de Tal';
        $std->exmedocup->respmonit->nrcrm = '12345678';
        $std->exmedocup->respmonit->ufcrm = 'AC';

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtMonit(
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