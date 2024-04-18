<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtCdBenefInController extends Controller{

    // S-2400
    public function evtCdBenefInC(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1;
        $std->indretif = 1;
        $std->nrrecibo = "1.7.1234567890123456789";
        $std->cpfbenef = '11111111111';
        $std->nmbenefic = 'NOME';
        $std->dtnascto = '1987-01-01';
        $std->dtinicio = '1987-01-01';
        $std->sexo = "M";
        $std->racacor = '1';
        $std->estciv = '1';
        $std->incfismen = 'S';
        $std->dtincfismen = '1999-12-12';

        $std->endereco = new \stdClass();
        $std->endereco->brasil = new \stdClass();
        $std->endereco->brasil->tplograd = 'AV';
        $std->endereco->brasil->dsclograd = 'Avenida da Paz';
        $std->endereco->brasil->nrlograd = '1000';
        $std->endereco->brasil->complemento = 'sobre loja';
        $std->endereco->brasil->bairro = 'Centro';
        $std->endereco->brasil->cep = '04178000';
        $std->endereco->brasil->codmunic = '3550308';
        $std->endereco->brasil->uf = 'SP';

        $std->endereco->exterior = new \stdClass();
        $std->endereco->exterior->paisresid = '805';
        $std->endereco->exterior->dsclograd = 'Bodega Street';
        $std->endereco->exterior->nrlograd = '1000';
        $std->endereco->exterior->complemento = null;
        $std->endereco->exterior->bairro = 'New City';
        $std->endereco->exterior->nmcid = 'Fakaofo';
        $std->endereco->exterior->codpostal = 'Z001';

        $std->dependente[0] = new \stdClass();
        $std->dependente[0]->tpdep = '03';
        $std->dependente[0]->nmdep = 'Luluzinha';
        $std->dependente[0]->dtnascto = '2010-04-12';
        $std->dependente[0]->cpfdep = '12345678901';
        $std->dependente[0]->sexodep = 'F';
        $std->dependente[0]->depirrf = 'S';
        $std->dependente[0]->incfismen = 'N';


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            /*********************************************************
             * Este evento MUDOU de nome na versão S 1.0
             *********************************************************/

            //cria o evento e retorna o XML assinado
            /*
            $xml = Event::evtCdBenefIn(
                $configJson,
                $std,
                $certificate,
                '2017-08-03 10:37:00' //opcional data e hora
            )->toXml();*/

            $xml = Event::s2400(
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