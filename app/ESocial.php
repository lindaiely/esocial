<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use NFePHP\Common\Certificate;
use NFePHP\eSocial\Event;
use NFePHP\eSocial\Tools;
use NFePHP\eSocial\Factories;
use NFePHP\eSocial\Common\Soap\SoapCurl;

/*

### EVENTOS versão S 1.2

-- EVENTOS INICIAIS - [1]
- S-1000 - Informações do Empregador/Contribuinte/Órgão Público
- S-1005 - Tabela de Estabelecimentos, Obras ou Unidades de Órgãos Públicos
- S-1010 - Tabela de Rubricas
- S-1020 - Tabela de Lotações Tributárias
- S-1070 - Tabela de Processos Administrativos/Judiciais
- S-2200 - Cadastramento Inicial do Vínculo e Admissão/Ingresso de Trabalhador


-- EVENTOS NAO PERIODICOS - [2]

- S-2190 - Registro Preliminar de Trabalhador
- S-2205 - Alteração de Dados Cadastrais do Trabalhador
- S-2206 - Alteração de Contrato de Trabalho/Relação Estatutária
- S-2210 - Comunicação de Acidente de Trabalho
- S-2220 - Monitoramento da Saúde do Trabalhador
- S-2230 - Afastamento Temporário
- S-2231 - Cessão/Exercício em Outro Órgão
- S-2240 - Condições Ambientais do Trabalho - Agentes Nocivos
- S-2298 - Reintegração/Outros Provimentos
- S-2299 - Desligamento
- S-2300 - Trabalhador Sem Vínculo de Emprego/Estatutário - Início
- S-2306 - Trabalhador Sem Vínculo de Emprego/Estatutário - Alteração Contratual
- S-2399 - Trabalhador Sem Vínculo de Emprego/Estatutário - Término
- S-2400 - Cadastro de Beneficiário - Entes Públicos - Início
- S-2405 - Cadastro de Beneficiário - Entes Públicos - Alteração
- S-2410 - Cadastro de Benefício - Entes Públicos - Início
- S-2416 - Cadastro de Benefício - Entes Públicos - Alteração
- S-2418 - Reativação de Benefício - Entes Públicos
- S-2420 - Cadastro de Benefício - Entes Públicos - Término
- S-2500 - Processo Trabalhista
- S-2501 - Informações de Tributos Decorrentes de Processo Trabalhista
- S-3000 - Exclusão de Eventos
- S-3500 - Exclusão de Eventos - Processo Trabalhista


-- EVENTOS PERIODICOS - [3]

- S-1200 - Remuneração de Trabalhador vinculado ao Regime Geral de Previd. Social
- S-1202 - Remuneração de Servidor vinculado ao Regime Próprio de Previd. Social
- S-1207 - Benefícios - Entes Públicos
- S-1210 - Pagamentos de Rendimentos do Trabalho
- S-1260 - Comercialização da Produção Rural Pessoa Física
- S-1270 - Contratação de Trabalhadores Avulsos Não Portuários
- S-1280 - Informações Complementares aos Eventos Periódicos
- S-1298 - Reabertura dos Eventos Periódicos
- S-1299 - Fechamento dos Eventos Periódicos

*/


class Esocial{
	var $config;
	var $certificate;
	var $eventos = array();
	var $empresa = array();
	var $configJson;
	var $pathCertificate = 'cert/';
	var $esocialEventos = array();
	var $request;
	var $response;
	var $loteEventos = array();
	var $qtdeLoteAtual = 0; //controle de eventos em um lote = max 50
	var $indiceLote = 0;
	var $senhaCertificado = '12345678';
	var $arquivoBinarioCertficado = '';
	var $tpInscCertificado = '';
	var $nrInscCertificado = '';
	var $enviarSoap = '';


    /**
     * faz a configuracao para envio ao ws
     * @param array $empresa | id | tpInsc 1-CNPJ, 2-CPF | nrInsc cnpj/cpf | nmRazao razao social
    */
	private function configEsocial($empresa){
		$this->empresa = $empresa;
		$this->carregaCertificadoEmpresa();
		if(strlen($empresa['nrInsc'])==14){
			$empresa['nrInsc'] = substr($empresa['nrInsc'],0,8);			
		}
		$this->config = [
		    'tpAmb' => $empresa['ambiente'],
		    //tipo de ambiente 1 - Producao; 2 - Producao restrita - dados reais;3 - Producao restrita - dados ficticios.
		    
		    //'verProc' => '2_4_02', //VersÃo do processo de emissÃo do evento. //Informar a versÃo do aplicativo emissor do evento.
		    //'eventoVersion' => '2.4.2', //versÃo do layout do evento
		    //'serviceVersion' => '1.4.1', //versÃo do webservice

		    'verProc' => 'S_1.2.0',
		    'eventoVersion' => 'S.1.2.0',
			'serviceVersion' => '1.5.0',

		    'empregador' => [
		        'tpInsc' => $empresa['tpInsc'], //1-CNPJ, 2-CPF
		        'nrInsc' => $empresa['nrInsc'], //numero do documento
		        'nmRazao' => $empresa['nmRazao'],
		    ],
		    'transmissor' => [
		        #'tpInsc' => 1, //1-CNPJ, 2-CPF
		        #'nrInsc' => '07994391000156'
		        'tpInsc' => $this->tpInscCertificado, //1-CNPJ, 2-CPF - fixo
		        'nrInsc' => $this->nrInscCertificado //cnpj da contando - fixo
		    ],
		];

		$this->configJson = json_encode($this->config, JSON_PRETTY_PRINT);
		
		try{			
			//carrega a classe responsavel por lidar com os certificados
			$content = file_get_contents(base_path('app/Certificado_Maximus2023.pfx'));
			//$content = $this->arquivoBinarioCertficado;
			$password = $this->senhaCertificado;
			$this->certificate = Certificate::readPfx($content, $password);
		}catch (\Exception $e) {
		    echo json_encode(array('erro'=>1,'msn'=>'Config: '.$e->getMessage()));
		    exit;
		}
	}

    public function verificaEventos(Array $empresa, $gerarEnviar = false){
        $this->empresa = $empresa;

        $this->addVerificaEventos();

        if ($gerarEnviar && count($this->esocialEventos) > 0) {
            $return = $this->gerarEventos($empresa, Tools::EVT);
            echo json_encode(array('erro' => 0, 'msn' => count($return) . ' Lote(s) enviado(s)!'));
        } else {
            if (count($this->esocialEventos) == 0) {
                echo json_encode(array('erro' => 1, 'msn' => 'Nenhum Evento Inicial Encontrado!'));
                exit;
            } else {
                echo json_encode($this->esocialEventos);
            }
        }
    }

    private function addVerificaEventos(){
        $eventos = [
            'S-1000',
            'S-1005',
            'S-1010',
            'S-1020',
            'S-1070',
            'S-1200',
            'S-1202',
            'S-1207',
            'S-1210',
            'S-1260',
            'S-1270',
            'S-1280',
            'S-1298',
            'S-1299',
            'S-2190',
            'S-2200',
            'S-2205',
            'S-2206',
            'S-2210',
            'S-2220',
            'S-2230',
            'S-2231',
            'S-2240',
            'S-2298',
            'S-2299',
            'S-2300',
            'S-2306',
            'S-2399',
            'S-2400',
            'S-2405',
            'S-2410',
            'S-2416',
            'S-2418',
            'S-2420',
            'S-2500',
            'S-2501',
            'S-3000',
            'S-3500',
        ];

        foreach ($eventos as $evento) {
            $this->esocialEventos[] = $this->$evento->verificaInclusao($this->empresa['id']);
        }
    }

    public function gerarEventos(Array $empresa, $grupo){
        if (!isset($empresa['id']) | (isset($empresa['id']) && !is_numeric($empresa['id']))) {
            echo json_encode(array('erro' => 1, 'msn' => 'Empresa não encontrada!'));
            exit;
        }
        $this->esocialEventos = array();
        $this->configEsocial($empresa);

        $this->eventos();

        $result = array();
        foreach ($this->loteEventos as $lote) {
            $eventos = array();
            $tbleventos = array();
            foreach ($lote as $detalhe) {
                $eventos[] = $detalhe['objeto'];
                $tbleventos[] = $detalhe['evento'];
            }
            $rs = $this->enviarEventos($grupo, $eventos); //eventos iniciais
            if ($rs['erro'] == 0) { //tem arquivos para enviar
                $this->processaEsocialEventos($tbleventos);
            } else {
                echo json_encode($rs);
                exit;
            }
            $result[] = $rs;
        }
        return $result;
    }

    public function eventos(){
        try {
            $this->evS1000(); // - S-1000 Informações do Empregador
            $this->evS1005(); // - S-1005 Tabela de Estabelecimentos, Obras ou Unidades de Órgãos Públicos
            $this->evS1010(); // - S-1010 Tabela de Rubricas
            $this->evS1020(); // - S-1020 Tabela de Lotações Tributárias
            $this->evS1070(); // - S-1070 Tabela de Processos Administrativos/Judiciais
            $this->evS1200(); // - S-1200 Remuneração de Trabalhador vinculado ao Regime Geral de Previd. Social
            $this->evS1202(); // - S-1202 Remuneração de Servidor vinculado ao Regime Próprio de Previd. Social
            $this->evS1207(); // - S-1207 Benefícios - Entes Públicos
            $this->evS1210(); // - S-1210 Pagamentos de Rendimentos do Trabalho
            $this->evS1260(); // - S-1260 Comercialização da Produção Rural Pessoa Física
            $this->evS1270(); // - S-1270 Contratação de Trabalhadores Avulsos Não Portuários
            $this->evS1280(); // - S-1280 Informações Complementares aos Eventos Periódicos
            $this->evS1298(); // - S-1298 Reabertura dos Eventos Periódicos
            $this->evS1299(); // - S-1299 Fechamento dos Eventos Periódicos
            $this->evS2190(); // - S-2190 Registro Preliminar de Trabalhador
            $this->evS2200(); // - S-2200 Cadastramento Inicial do Vínculo e Admissão/Ingresso de Trabalhador
            $this->evS2205(); // - S-2205 Alteração de Dados Cadastrais do Trabalhador
            $this->evS2206(); // - S-2206 Alteração de Contrato de Trabalho/Relação Estatutária
            $this->evS2210(); // - S-2210 Comunicação de Acidente de Trabalho
            $this->evS2220(); // - S-2220 Monitoramento da Saúde do Trabalhador
            $this->evS2230(); // - S-2230 Afastamento Temporário
            $this->evS2231(); // - S-2231 Cessão/Exercício em Outro Órgão
            $this->evS2240(); // - S-2240 Condições Ambientais do Trabalho - Agentes Nocivos
            $this->evS2298(); // - S-2298 Reintegração/Outros Provimentos
            $this->evS2299(); // - S-2299 Desligamento
            $this->evS2300(); // - S-2300 Trabalhador Sem Vínculo de Emprego/Estatutário - Início
            $this->evS2306(); // - S-2306 Trabalhador Sem Vínculo de Emprego/Estatutário - Alteração Contratual
            $this->evS2399(); // - S-2399 Trabalhador Sem Vínculo de Emprego/Estatutário - Término
            $this->evS2400(); // - S-2400 Cadastro de Beneficiário - Entes Públicos - Início
            $this->evS2405(); // - S-2405 Cadastro de Beneficiário - Entes Públicos - Alteração
            $this->evS2410(); // - S-2410 Cadastro de Benefício - Entes Públicos - Início
            $this->evS2416(); // - S-2416 Cadastro de Benefício - Entes Públicos - Alteração
            $this->evS2418(); // - S-2418 Reativação de Benefício - Entes Públicos
            $this->evS2420(); // - S-2420 Cadastro de Benefício - Entes Públicos - Término
            $this->evS2500(); // - S-2500 Processo Trabalhista
            $this->evS2501(); // - S-2501 Informações de Tributos Decorrentes de Processo Trabalhista
            $this->evS3000(); // - S-3000 Exclusão de Eventos
            $this->evS3500(); // - S-3500 - Exclusão de Eventos - Processo Trabalhista
        } catch (\Exception $e) {
            echo json_encode(array('erro' => 1, 'msn' => 'Gerando Eventos: ' . $e->getMessage()));
            exit;
        }
    }

    private function evS1000(){
        $std = $this->config['empregador']; // Utiliza os dados do empregador configurados anteriormente

        // Verifica se os dados foram obtidos com sucesso
        if ($std != null) {
            $evento = Event::evtInfoEmpregador($this->configJson, $std);

            // Adiciona o identificador do evento ao objeto de dados do evento
            $evento->dados->ideventoxml = $evento->evtid;

            // Adiciona o evento ao lote de eventos
            $this->addEventosLotes($evento, $evento->dados);
        }
    }

    private function evS1005(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtTabEstab($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1010(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtTabRubrica($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1020(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtTabLotacao($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1070(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtTabProcesso($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1200(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtRemun($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1202(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtRmnRPPS($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1207(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtBenPrRP($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1210(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtPgtos($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1260(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtComProd($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1270(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtContratAvNP($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1280(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtInfoComplPer($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1298(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtReabreEvPer($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS1299(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtFechaEvPer($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2190(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtAdmPrelim($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2200(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtAdmissao($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2205(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtAltCadastral($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}
    
    private function evS2206(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtAltContratual($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2210(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtCAT($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2220(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtMonit($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2230(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtAfastTemp($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2231(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtCessao($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2240(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtExpRisco($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2298(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtReintegr($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2299(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtDeslig($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2300(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtTSVInicio($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2306(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtTSVAltContr($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2399(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtTSVTermino($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2400(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtCdBenefIn($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2405(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtCdBenefAlt($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2410(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtCdBenIn($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2416(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtCdBenAlt($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2418(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtReativBen($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2420(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtCdBenTerm($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2500(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtProcTrab($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS2501(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtContProc($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS3000(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtExclusao($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function evS3500(){
		$std = $this->config['empregador'];
	    if($stds!=null&&count($stds)>0){
	    	foreach ($stds as $std){
	    		$evento = Event::evtExcProcTrab($this->configJson, $std);
                $evento->dados->ideventoxml = $evento->evtid;
                $this->addEventosLotes($evento, $evento->dados);
	    	}
	    }
	}

    private function addEventosLotes($objEvento, $tblEvento){
        if ($this->qtdeLoteAtual % 50 == 0) {
            $this->indiceLote++;
        }

        // Verifica se o lote atual já foi inicializado
        if (!isset($this->loteEventos[$this->indiceLote])) {
            $this->loteEventos[$this->indiceLote] = array();
        }

        $this->loteEventos[$this->indiceLote][] = array('objeto' => $objEvento, 'evento' => $tblEvento);
        $this->qtdeLoteAtual++;
    }


    private function enviarEventos($eventos){
        if (count($eventos) > 0) {
            try {
                // Utilize esta linha para enviar os eventos
                //$soap = new SoapCurl($this->certificate, null, false);

                // Instancia a classe Tools para manipular os eventos
                $tools = new Tools($this->configJson, $this->certificate);

                // Carrega a classe SOAP para comunicação
                //$tools->loadSoapClass($soap);

                // Envia os eventos
                $response = $tools->enviarLoteEventos(1, $eventos, $this->enviarSoap); // O segundo parâmetro é o grupo de eventos, que foi definido como 1 para EVT_INICIAIS, você pode ajustar conforme necessário

                // Salva os dados da requisição e resposta
                $this->response = $response;
                $this->request = $tools->lastRequest;

                return array('erro' => 0, 'msn' => 'Lote enviado!', 'response' => $response);
            } catch (\Exception $e) {
                return array('erro' => 1, 'msn' => 'Enviando: ' . $e->getMessage());
            }
        } else {
            return array('erro' => 1, 'msn' => 'Nenhum evento para Envio!');
        }
    }

	private function enviarEventosXml($eventos){
        if (count($eventos) > 0) {
            try {
                // Utilize esta linha para enviar os eventos
                //$soap = new SoapCurl($this->certificate, null, false);

                // Instancia a classe Tools para manipular os eventos
                $tools = new Tools($this->configJson, $this->certificate);

                // Carrega a classe SOAP para comunicação
                //$tools->loadSoapClass($soap);

                // Envia os eventos
                $response = $tools->enviarLoteXmlEventos(1, $eventos, $this->enviarSoap); // O segundo parâmetro é o grupo de eventos, que foi definido como 1 para EVT_INICIAIS, você pode ajustar conforme necessário

                // Salva os dados da requisição e resposta
                $this->response = $response;
                $this->request = $tools->lastRequest;

                return array('erro' => 0, 'msn' => 'Lote enviado!', 'response' => $response);
            } catch (\Exception $e) {
                return array('erro' => 1, 'msn' => 'Enviando: ' . $e->getMessage());
            }
        } else {
            return array('erro' => 1, 'msn' => 'Nenhum evento para Envio!');
        }
    }

    public function consultaLote($empresa,$protocolo){
		$this->configEsocial($empresa);		
		try {
		    //$soap = new SoapCurl($this->certificate ,null,true);
		    $tools = new Tools($this->configJson, $this->certificate);
		    //carrega a classe responsÃ¡vel pelo envio SOAP
		    //nesse caso um envio falso
		    //$tools->loadSoapClass($soap);

		    //executa a consulta
		    $response = $tools->consultarLoteEventos($protocolo);			    
		    $this->request = $tools->lastRequest;
		    return xmlToArray($response);
		} catch (\Exception $e) {		    
		    echo json_encode(array('erro'=>1,'msn'=>'Consulta: '.$e->getMessage()));
		}
	}

	public function consultaEventosEmp($empresa,$protocolo){
		$this->configEsocial($empresa);		
		try {
		    //$soap = new SoapCurl($this->certificate ,null,true);
		    $tools = new Tools($this->configJson, $this->certificate);
			//$tools->loadSoapClass($soap);

		    //executa a consulta
		    $response = $tools->consultaEventosEmpregador($protocolo);			    
		    $this->request = $tools->lastRequest;
		    return xmlToArray($response);
		} catch (\Exception $e) {		    
		    echo json_encode(array('erro'=>1,'msn'=>'Consulta: '.$e->getMessage()));
		}
	}

	public function consultaEventosTab($empresa,$protocolo){
		$this->configEsocial($empresa);		
		try {
		    //$soap = new SoapCurl($this->certificate ,null,true);
		    $tools = new Tools($this->configJson, $this->certificate);
			//$tools->loadSoapClass($soap);

		    //executa a consulta
		    $response = $tools->consultarEventosTabela($protocolo);			    
		    $this->request = $tools->lastRequest;
		    return xmlToArray($response);
		} catch (\Exception $e) {		    
		    echo json_encode(array('erro'=>1,'msn'=>'Consulta: '.$e->getMessage()));
		}
	}

	public function consultaEventosTrab($empresa,$protocolo){
		$this->configEsocial($empresa);		
		try {
		    //$soap = new SoapCurl($this->certificate ,null,true);
		    $tools = new Tools($this->configJson, $this->certificate);
			//$tools->loadSoapClass($soap);

		    //executa a consulta
		    $response = $tools->consultarEventosTrabalhador($protocolo);			    
		    $this->request = $tools->lastRequest;
		    return xmlToArray($response);
		} catch (\Exception $e) {		    
		    echo json_encode(array('erro'=>1,'msn'=>'Consulta: '.$e->getMessage()));
		}
	}


}