<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtCdBenInController extends Controller{

    // S-2410
    public function evtCdBenInC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 1; //obrigatorio
        $std->nrrecibo = '1.4.1234567890123456789'; //opcional
        $std->cpfbenef = '12345678901'; //obrigatorio
        $std->matricula = '12345'; //opcional
        $std->cnpjorigem = '12345678901234'; //opcional
        $std->cadini = 'S'; //obrigatorio
        $std->indsitbenef = '1'; //opcional
        $std->nrbeneficio = '12345'; //obrigatorio
        $std->dtinibeneficio = '2021-01-01'; //obrigatorio
        $std->dtpublic = '2021-01-01'; //obrigatorio
        $std->tpbeneficio = "0805"; //obrigatorio
        $std->tpplanrp = 0; //obrigatorio
        $std->dsc = "bla bla bla bla"; //opcional
        $std->inddecjud = 'N'; //opcional

        //campo opcional
        $std->infopenmorte = new \stdClass();
        $std->infopenmorte->tppenmorte = 1; //obrigatório
        //campo opcional
        $std->infopenmorte->instpenmorte = new \stdClass();
        $std->infopenmorte->instpenmorte->cpfinst = '12345678901'; //obrigatório
        $std->infopenmorte->instpenmorte->dtinst = '2020-12-12'; //obrigatório

        //campo opcional
        $std->sucessaobenef = new \stdClass();
        $std->sucessaobenef->cnpjorgaoant = '12345678901234'; //obrigatório
        $std->sucessaobenef->nrbeneficioant = 'cd2345';  //obrigatório
        $std->sucessaobenef->dttransf = '2020-12-20';  //obrigatório
        $std->sucessaobenef->observacao = 'texto livre'; //opcional

        //campo opcional
        $std->mudancacpf = new \stdClass();
        $std->mudancacpf->cpfant = '12345678901'; //obrigatório
        $std->mudancacpf->nrbeneficioant= 'cd2345'; //obrigatório
        $std->mudancacpf->dtaltcpf = '2019-01-01'; //obrigatório
        $std->mudancacpf->observacao = 'outro texto livre'; //opcional

        //campo opcional
        $std->infobentermino = new \stdClass();
        $std->infobentermino->dttermbeneficio = '2121-05-01';
        $std->infobentermino->mtvtermino = '11';


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtCdBenIn(
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