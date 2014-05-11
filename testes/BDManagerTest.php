<?php

require_once '../autoloader.php';

use classes_\BDManager as BDManager;


class BDManagerTest extends PHPUnit_Framework_TestCase
{
    
    public function testInsereFiltro()
    {
        $bd = new BDManager();
        $nome = 'Physique';
        $this->assertTrue($bd->insereFiltro($nome));
        $filtro = $bd->selecionaFiltro(null, $nome);
        $this->assertTrue($bd->insereFiltro('Fluyds', $filtro->getId()));
        $this->assertFalse($bd->insereFiltro($nome));
    }

}