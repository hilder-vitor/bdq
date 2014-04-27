<?php

require_once ('../fonte/BDManager.class.php');

$bd = new BDManager();

echo "=======\n=========\n";
echo "=======\n=========\n";
echo "=======\n=========\n";

var_dump($bd->selecionaFiltro(4));


?>
