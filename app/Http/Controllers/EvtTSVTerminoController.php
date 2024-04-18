<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtTSVTerminoController extends Controller{

    // S-2399
    public function evtTSVTerm(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 1;
        $std->nrrecibo = '1.1.1234567890123456789';
        $std->indguia = 1;
        $std->cpftrab = '12345678901';
        $std->matricula = 'ABC12345678902';
        $std->codcateg = 101;
        $std->dtterm = '2017-12-22';
        $std->mtvdesligtsv = '01';
        $std->pensalim = 3;
        $std->percaliment = 10.00;
        $std->vralim = 600.23;
        $std->nrproctrab = "12345678901234567890";
        $std->novocpf = "12345678901";


        $std->verbasresc = new \stdClass();
        $std->verbasresc->dmdev[1] = new \stdClass();
        $std->verbasresc->dmdev[1]->idedmdev = 'ksksksksksksksk';

        $std->verbasresc->dmdev[1]->ideestablot[1] = new \stdClass();
        $std->verbasresc->dmdev[1]->ideestablot[1]->tpinsc = 1;
        $std->verbasresc->dmdev[1]->ideestablot[1]->nrinsc = '12345678901234';
        $std->verbasresc->dmdev[1]->ideestablot[1]->codlotacao = 'assss';

        $std->verbasresc->dmdev[1]->ideestablot[1]->detverbas[1] = new \stdClass();
        $std->verbasresc->dmdev[1]->ideestablot[1]->detverbas[1]->codrubr = '2323dffdf';
        $std->verbasresc->dmdev[1]->ideestablot[1]->detverbas[1]->idetabrubr = 'sdser234';
        $std->verbasresc->dmdev[1]->ideestablot[1]->detverbas[1]->qtdrubr = 256.20;
        $std->verbasresc->dmdev[1]->ideestablot[1]->detverbas[1]->fatorrubr = 25.56;
        $std->verbasresc->dmdev[1]->ideestablot[1]->detverbas[1]->vrrubr = 12345.56;
        $std->verbasresc->dmdev[1]->ideestablot[1]->detverbas[1]->indapurir = 0;

        $std->verbasresc->dmdev[1]->ideestablot[1]->infosimples = new \stdClass();
        $std->verbasresc->dmdev[1]->ideestablot[1]->infosimples->indsimples = 1;

        $std->verbasresc->procjudtrab[1] = new \stdClass();
        $std->verbasresc->procjudtrab[1]->tptrib = 2;
        $std->verbasresc->procjudtrab[1]->nrprocjud = '12345678901234567890';
        $std->verbasresc->procjudtrab[1]->codsusp = '12345678901234';
        $std->verbasresc->infomv = new \stdClass();
        $std->verbasresc->infomv->indmv = 3;
        $std->verbasresc->infomv->remunoutrempr[1] = new \stdClass();
        $std->verbasresc->infomv->remunoutrempr[1]->tpinsc = 1;
        $std->verbasresc->infomv->remunoutrempr[1]->nrinsc = '12345678901234';
        $std->verbasresc->infomv->remunoutrempr[1]->codcateg = 905;
        $std->verbasresc->infomv->remunoutrempr[1]->vlrremunoe = 2598.56;

        $std->remunaposterm =  new \stdClass(); //Opcional
        //Indicativo de situação de remuneração após o término.
        //Informação obrigatória se {dtTerm}(2399_infoTSVTermino_dtTerm) >= [2023-01-16].
        $std->remunaposterm->indremun = 1; //Opcional
        // 1 - Quarentena
        // 2 - Término reconhecido judicialmente com data anterior a competências com remunerações já informadas no eSocial
        $std->remunaposterm->dtfimremun = '2023-01-22'; //Obrigatório
        //Preencher com a data final da quarentena a que está sujeito o trabalhador. No caso de término
        //reconhecido judicialmente com data anterior a competências com remunerações já informadas no eSocial,
        //informar o último dia trabalhado.
        //Deve ser uma data posterior a {dtTerm}(2399_infoTSVTermino_dtTerm).


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtTSVTermino(
                $configJson,
                $std,
                $certificate,
                '2017-08-03 10:37:00'
            )->toXml();

            //$xml = Evento::s2399($json, $std, $certificate)->toXML();
            //$json = Event::evtTSVTermino($configjson, $std, $certificate)->toJson();

            header('Content-type: text/xml; charset=UTF-8');
            echo $xml;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}