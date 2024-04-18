<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;

class EvtFechaEvPerController extends Controller{

    // S-1299
    public function evtFechaEv(Request $request){

        $config = include('config.php');
        $configJson = json_encode($config, JSON_PRETTY_PRINT);

        $std = new \stdClass();
        //$std->sequencial = 1; //Opcional
        $std->indapuracao = 1; //obrigatorio
        //Indicativo de período de apuração. 1 - Mensal 2 - Anual (13° salário)

        $std->perapur = '2017-08';  //obrigatorio
        //Informar o mês/ano (formato AAAA-MM) de referência das informações, se indApuracao for igual a [1],
        //ou apenas o ano (formato AAAA), se indApuracao for igual a [2].

        $std->indguia = 1; //opcional
        //Indicativo do tipo de guia. 1 - Documento de Arrecadação do eSocial - DAE

        //informações para o fechamento
        $std->infofech = new \stdClass();
        $std->infofech->evtremun = 'N';  //obrigatorio
        //Possui informações relativas a remuneração de trabalhadores ou provento/pensão de
        //beneficiários no período de apuração?
        //Se for igual a [S], deve existir evento de remuneração (S-1200, S-1202, S-1207, S-2299 ou S-2399)
        //para o período de apuração. Caso contrário, não deve existir evento de remuneração.

        $std->infofech->evtpgtos = 'N';  //obrigatorio
        //Possui informações relativas a remuneração de trabalhadores ou provento/pensão de beneficiários no período de apuração?
        //Se for igual a [S], deve existir evento de remuneração (S-1200, S-1202, S-1207, S-2299 ou S-2399) para o
        //período de apuração, considerando o campo {indGuia}(1299_ideEvento_indGuia). Caso contrário, não deve existir
        //evento de remuneração.

        $std->infofech->evtcomprod = 'N';  //obrigatorio
        //Possui informações de comercialização de produção?
        //Se for igual a [S], deve existir o evento S-1260 para o período de apuração. Caso contrário, não deve existir o evento.

        $std->infofech->evtcontratavnp = 'N';  //obrigatorio
        //Contratou, por intermédio de sindicato, serviços de trabalhadores avulsos não portuários?
        //Se for igual a [S], deve existir o evento S-1270 para o período de apuração. Caso contrário, não deve existir o evento.

        $std->infofech->evtinfocomplper = 'N'; //obrigatorio
        //Possui informações de desoneração de folha de
        //pagamento ou, sendo empresa enquadrada no Simples,
        //possui informações sobre a receita obtida em atividades
        //cuja contribuição previdenciária incidente sobre a folha de
        //pagamento é concomitantemente substituída e não
        //substituída?

        $std->infofech->indexcapur1250 = 'S'; //opcional mas aceita somente NULL ou S
        //Indicativo de exclusão de apuração das aquisições de produção rural (eventos S-1250) do período de apuração.
        //Não informar se perApur >= [2021-05] ou se indApuracao = [2]. Preenchimento obrigatório caso o
        //campo tenha sido informado em fechamento anterior do mesmo período de apuração

        $std->infofech->transdctfweb = 'S'; //opcional
        // Solicitação de transmissão imediata da DCTFWeb.
        // Não informar se perApur < [2021-10]. Preenchimento obrigatório se perApur >= [2021-10] e
        // (classTrib em S-1000 = [04] ou indGuia estiver informado).

        $std->infofech->naovalid = 'S'; //opcional
        //Indicativo de não validação das regras de fechamento, para que os grandes contribuintes possam reduzir o tempo de processamento do evento.
        //O preenchimento deste campo implica a não execução da REGRA_VALIDA_FECHAMENTO_FOPAG.
        //Não informar se {procEmi}(1299_ideEvento_procEmi) for diferente de [1]


        try {
            //carrega a classe responsavel por lidar com os certificados
            $content = file_get_contents(base_path('app/Http/Controllers/Certificado_Maximus2023.pfx'));
            $password = '12345678';
            $certificate = Certificate::readPfx($content, $password);

            //cria o evento e retorna o XML assinado
            $xml = Event::evtFechaEvPer(
                $configJson,
                $std,
                $certificate,
                '2017-08-03 10:37:00'
            )->toXml();

            header('Content-type: text/xml; charset=UTF-8');
            echo $xml;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}