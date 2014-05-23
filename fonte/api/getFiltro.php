<?php require_once '../../autoloader.php';

use classes_\Json as Json;

/* ----------------------------------------------------------
 *      Página que recebe requisições dos clientes e busca
 * por filtros, devolvendo-as no formato json.
 * ----------------------------------------------------------  */

if (!isset($_GET['query'])){
    exit(json_encode(array('status' => 1, 'msg' => 'Query não foi passada.')));
}

// extrai as variáveis passadas por GET
extract($_GET);

// se está pedindo os filtros do primeiro nível
if (preg_match('/^primeiro_?nivel$/i', $query)){
    exit(Json::devolveJsonFiltrosFilhos(0));
}


if (preg_match('/^filhos$/i', $query)){
    if (isset($idFiltroPai)){
        exit(Json::devolveJsonFiltrosFilhos($idFiltroPai));
    }
    if (isset($nomeFiltroPai)){
        exit(Json::devolveJsonFiltrosFilhos(null, $nomeFiltroPai));
    }
    exit(json_encode(array('status' => 3,
		      'msg' => 'Nem o idFiltroPai nem o nomeFiltroPai foi passado.')));
    
}

exit(json_encode(array('status' => 2,
		      'msg' => 'A query deve ser uma das seguintes:'
			     . ' "primeiroNivel",'
			     . ' "filhos"')));
