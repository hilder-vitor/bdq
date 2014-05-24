<?php  require_once '../../autoloader.php';

use classes_\BDManager as BDManager;
use classes_\Json as Json;
use classes_\Questao;

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
    if (isset($tipo)){
        if (preg_match('/^dissertativa$/i', $tipo)){
	   $tipo = Questao::QUESTAO_DISSERTATIVA;
        }else if (preg_match('/^teste$/i', $tipo)
		|| preg_match('/^alternativa$/i', $tipo)){
	   $tipo = Questao::QUESTAO_ALTERNATIVA;
        }else{
	   $tipo = null;
        }
    }else{
        $tipo = null;
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
    echo "selecionaQuestao($idInicial, $qntQuestoes, $filtros,$tipo);<br>";
    $questoes = $bd->selecionaQuestao($idInicial, $qntQuestoes, $filtros,$tipo);
    
    exit(Json::transformaVetorDeQuestoesEmJson($questoes));    
}