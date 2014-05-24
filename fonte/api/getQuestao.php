<?php  require_once '../../autoloader.php';

use \classes_\BDManager as BDManager;
use classes_\Json as Json;

/* ----------------------------------------------------------
 *      Página que recebe requisições dos clientes e busca
 * por questões, devolvendo-as no formato json.
 * ----------------------------------------------------------  */

// extrai as variáveis passadas por GET
extract($_GET);

// busca de questões por id de filtro
if (isset($idsFiltros)){
    if (!isset($idInicial)){
        $idInicial = 0;
    }
    if (!isset($qntQuestoes)){
        $qntQuestoes = 10;
    }else{
        if ($qntQuestoes > 100){
	   $qntQuestoes = 100;
        }
    }
    
    $bd = new BDManager();
    $vetQuestoes = array();
    $vetFiltros = array();
    $vetIdsFiltros = explode('|', $idsFiltros);
    $filtros = array();
    foreach($vetIdsFiltros as $idFiltro){
        $filtro = $bd->selecionaFiltro($idFiltro,null,null,true);
        if (null != $filtro){
	   $filtros[] = $filtro;
        }
    }
    $questoes = $bd->selecionaQuestao($idInicial, $qntQuestoes, $filtros);
    exit(Json::transformaVetorDeQuestoesEmJson($questoes));    
}