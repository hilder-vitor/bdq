<?php require_once '../../autoloader.php';

use classes_\BDManager;


/* ----------------------------------------------------------
 *      Página que recebe requisições dos clientes e busca
 * por filtros, devolvendo-as no formato json.
 * ----------------------------------------------------------  */


// ----------------- DEFININDO FUNÇÕES USADAS NESTA PÁGINA ----------------
function devolveJsonFiltrosFilhos($idFiltroPai){
    $bd = new BDManager();
    $filtros = $bd->selecionaFiltro(null, null, $idFiltroPai,false);
    $jsonFiltros = array();
    foreach($filtros as $filtro){
        $jsonFiltros[] = transformaFiltroEmArray($filtro);
    }
    exit(json_encode(array('status' => 0,
		      'msg' => 'OK',
		      'filtros' => $jsonFiltros)));
}

function transformaFiltroEmArray(Filtro $filtro){
    return array('idFiltro' => $filtro->getId(),
	       'nome' => $filtro->getNome(),
	       'idPai' => $filtro->getIdPai());
}

// --------------  FIM DA DEFINIÇÃO DE FUNÇÕES --------------------

if (!isset($_GET['query'])){
    exit(json_encode(array('status' => 1, 'msg' => 'Query não foi passada.')));
}

// extrai as variáveis passadas por GET
extract($_GET);

// se está pedindo os filtros do primeiro nível
if (preg_match('/^primeiro_?nivel$/i', $query)){
    devolveJsonFiltrosFilhos(0);
}


if (preg_match('/^filhos$/i', $query)){
    if (!isset($idFiltroPai)){
        exit(json_encode(array('status' => 3,
		      'msg' => 'O id do filtro pai não foi passado.')));
    }
    devolveJsonFiltrosFilhos($idFiltroPai);
}

exit(json_encode(array('status' => 2,
		      'msg' => 'A query deve ser uma das seguintes:'
			     . ' "primeiroNivel",'
			     . ' "filhos"')));
