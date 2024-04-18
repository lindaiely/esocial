<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::match(['post'], '/esocial/evt-info', [EvtInfoEmpregadorController::class, 'evtInfoEmpregadorC']);
Route::match(['post'], '/esocial/evt-admissao', [EvtAdmissaoController::class, 'evtAdm']);
Route::match(['post'], '/esocial/evt-cessao', [EvtCessaoController::class, 'evtCessaoC']);
Route::match(['post'], '/esocial/evt-pgtos', [EvtPgtosController::class, 'evtPgtosC']);
Route::match(['post'], '/esocial/evt-rmn', [EvtRmnRPPSController::class, 'rmnRpps']);
Route::match(['post'], '/esocial/evt-admprelim', [EvtAdmPrelimController::class, 'evtAdmPrelimC']);
Route::match(['post'], '/esocial/evt-deslig', [EvtDesligController::class, 'evtDesligC']);
Route::match(['post'], '/esocial/evt-afast', [EvtAfastTempController::class, 'evtAfast']);
Route::match(['post'], '/esocial/evt-exclusao', [EvtExclusaoController::class, 'evtExclusaoC']);
Route::match(['post'], '/esocial/evt-excproc', [EvtExcProcTrabController::class, 'evtExcProc']);

Route::match(['post'], '/esocial/evt-altcad', [EvtAltCadastralController::class, 'evtAltCad']);
Route::match(['post'], '/esocial/evt-exprisco', [EvtExpRiscoController::class, 'evtExpRiscoC']);
Route::match(['post'], '/esocial/evt-altcont', [EvtAltContratualController::class, 'evtAltCont']);
Route::match(['post'], '/esocial/evt-fechaev', [EvtFechaEvPerController::class, 'evtFechaEv']);
Route::match(['post'], '/esocial/evt-tabEstab', [EvtTabEstabController::class, 'evtTabEstabC']);
Route::match(['post'], '/esocial/evt-infComp', [EvtInfoComplPerController::class, 'evtInfComp']);
Route::match(['post'], '/esocial/evt-tabLot', [EvtTabLotacaoController::class, 'evtTabLot']);
Route::match(['post'], '/esocial/evt-benP', [EvtBenPrRPController::class, 'evtBenP']);
Route::match(['post'], '/esocial/evt-tabP', [EvtTabProcessoController::class, 'evtTabP']);
Route::match(['post'], '/esocial/evt-cat', [EvtCATController::class, 'evtCatC']);

Route::match(['post'], '/esocial/evt-monit', [EvtMonitController::class, 'evtMonitC']);
Route::match(['post'], '/esocial/evt-tabRub', [EvtTabRubricaController::class, 'evtTabRub']);
Route::match(['post'], '/esocial/evt-cdBenefIn', [EvtCdBenefInController::class, 'evtCdBenefInC']);
Route::match(['post'], '/esocial/evt-cdBenefAlt', [EvtCdBenefAltController::class, 'evtCdBenefAltC']);
Route::match(['post'], '/esocial/evt-cdBenIn', [EvtCdBenInController::class, 'evtCdBenInC']);
Route::match(['post'], '/esocial/evt-cdBenAlt', [EvtCdBenAltController::class, 'evtCdBenAltC']);
Route::match(['post'], '/esocial/evt-reativBen', [EvtReativBenController::class, 'evtReativBenC']);
Route::match(['post'], '/esocial/evt-cdBenTerm', [EvtCdBenTermController::class, 'evtCdBenTermC']);
Route::match(['post'], '/esocial/evt-tsvalt', [EvtTSVAltContrController::class, 'evtTSVAltC']);
Route::match(['post'], '/esocial/evt-reabreEv', [EvtReabreEvPerController::class, 'evtReabreEvPerC']);

Route::match(['post'], '/esocial/evt-tsvIni', [EvtTSVInicioController::class, 'evtTSVIni']);
Route::match(['post'], '/esocial/evt-contrat', [EvtContratAvNPController::class, 'evtContratAvNPC']);
Route::match(['post'], '/esocial/evt-reinteger', [EvtReintegrController::class, 'evtReintegrC']);
Route::match(['post'], '/esocial/evt-tsvTerm', [EvtTSVTerminoController::class, 'evtTSVTerm']);
Route::match(['post'], '/esocial/evt-remun', [EvtRemunController::class, 'evtRemunC']);
Route::match(['post'], '/esocial/evt-procTrab', [EvtProcTrabController::class, 'evtProcTrabC']);
Route::match(['post'], '/esocial/evt-contProc', [EvtContProcController::class, 'evtContProcC']);
Route::match(['post'], '/esocial/evt-comProd', [EvtComProdController::class, 'evtComProdC']);

// Rotas de consulta e envio de lotes

Route::match(['post'], '/esocial/consulta-lote', [ConsultaLoteController::class, 'consultaLote']);