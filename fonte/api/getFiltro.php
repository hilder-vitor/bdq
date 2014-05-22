<?php require_once '../../autoloader.php';

use classes_\BDManager;


/* ----------------------------------------------------------
 *      Página que recebe requisições dos clientes e busca
 * por filtros, devolvendo-as no formato json.
 * ----------------------------------------------------------  */


if (!isset($_GET['query'])){
    exit(json_encode(array('status' => 1, 'msg' => 'Query não foi passada.')));
}
extract($_GET);

if ($query == 'primeiroNivel'){
    $bd = new BDManager();
    $filtros = $bd->selecionaFiltro(null, null, 0,false);
    $jsonFiltros = array();
    foreach($filtros as $filtro){
        $jsonFiltros[] = array('idFiltro' => $filtro->getId(),
			     'nome' => $filtro->getNome(),
			     'idPai' => 0);
    }
    exit(json_encode($jsonFiltros));
}
