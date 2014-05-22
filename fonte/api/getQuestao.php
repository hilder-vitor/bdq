<?php
require_once '../../autoloader.php';

/* ----------------------------------------------------------
 *      Página que recebe requisições dos clientes e busca
 * por questões, devolvendo-as no formato json.
 * ----------------------------------------------------------  */


if (!isset($_GET['query'])){
    echo json_encode(array('status' => 1, 'msg' => 'Query não foi passada.'));
}
extract($_GET);

// busca de questões por filtro
if (isset($filtros)){
    $vetFiltros = explode('|', $filtros);
    echo "$filtros<br/>";
    var_dump($vetFiltros);
}