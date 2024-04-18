<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtTSVAltContrController extends Controller{

    // S-2306
    public function evtTSVAltC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 2;
        $std->nrrecibo = '1.1.1234567890123456789';

        $std->trabsemvinculo = new \stdClass();
        $std->trabsemvinculo->cpftrab = '11111111111';
        $std->trabsemvinculo->matricula = 'ABC11111111111';
        $std->trabsemvinculo->codcateg = '101'; //Opcional

        $std->tsvalteracao = new \stdClass();
        $std->tsvalteracao->dtalteracao = '2017-08-25';
        $std->tsvalteracao->natatividade = 1;

        $std->cargofuncao = new \stdClass();
        $std->cargofuncao->nmcargo = 'Empilhador de caixas';
        $std->cargofuncao->cbocargo = '123456';
        $std->cargofuncao->nmfuncao = 'Empilhador de caixas';
        $std->cargofuncao->cbofuncao = '123456';

        $std->remuneracao = new \stdClass();
        $std->remuneracao->vrsalfx = 1500;
        $std->remuneracao->undsalfixo = 6;
        $std->remuneracao->dscsalvar = 'desc salario variavel';

        $std->dirigentesindical = new \stdClass();
        $std->dirigentesindical->tpregprev = 1;

        $std->trabcedido = new \stdClass();
        $std->trabcedido->tpregprev = 1;

        $std->mandelet = new \stdClass();
        $std->mandelet->indremuncargo = 'S';
        $std->mandelet->tpregprev = 1;

        $std->estagiario = new \stdClass();
        $std->estagiario->natestagio = 'O';
        $std->estagiario->nivestagio = 1;
        $std->estagiario->areaatuacao = 'ATUACAO';
        $std->estagiario->nrapol = '12345681';
        $std->estagiario->dtprevterm = '2017-08-25';

        $std->estagiario->instensino = new \stdClass();
        $std->estagiario->instensino->cnpjinstensino = '11111111111111';
        $std->estagiario->instensino->nmrazao = 'INSTITUICAO DE ENSINO';
        $std->estagiario->instensino->dsclograd = 'lrogradouro';
        $std->estagiario->instensino->nrlograd = "numero";
        $std->estagiario->instensino->bairro = "bairro";
        $std->estagiario->instensino->cep = "12345678";
        $std->estagiario->instensino->codmunic = "1234567";
        $std->estagiario->instensino->uf = "AC";

        $std->estagiario->ageintegracao = new \stdClass();
        $std->estagiario->ageintegracao->cnpjagntinteg = '11111111111111';

        $std->estagiario->supervisor = new \stdClass();
        $std->estagiario->supervisor->cpfsupervisor = '11111111111';

        $std->localtrabgeral = new \stdClass();
        $std->localtrabgeral->tpinsc = 1;
        $std->localtrabgeral->nrinsc = '12345678901234';
        $std->localtrabgeral->desccomp = 'Bla bla bla';

        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtTSVAltContr(
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