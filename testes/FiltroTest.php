<?php

require_once '../autoloader.php';

use classes_\Filtro as Filtro;

class FiltroTest extends PHPUnit_Framework_TestCase
{
    
    public function testGetId()
    {
        $filtro = new Filtro(4, 'Matemática');
        $this->assertEquals(4, $filtro->getId());
        
        $filtro = new Filtro(0, 'Matemática');
        $this->assertEquals(0, $filtro->getId());
    }

    public function testGetListaIdsFilhos()
    {
        $filtro = new Filtro(4, 'Matemática');
        $this->assertEquals(array(), $filtro->getListaIdsFilhos());
        
        $f1 = new Filtro(3, 'Estatística');
        $filtro->adicionaFilho($f1);
        $e = array(3);
        $this->assertEquals($e, $filtro->getListaIdsFilhos());
        $f2 = new Filtro(5, 'Probabilidade');
        $f2->adicionaFilho(new Filtro(6, 'Esperança'));
        $f3 = new Filtro(16, 'Baysiano');
        $f3->adicionaFilho(new Filtro(116, 'sub-baysiano'));
        $f1->adicionaFilho(new Filtro(8, 'Mediana'));
        $f1->adicionaFilho(new Filtro(18,'Amostragem'));
        $f1->adicionaFilho($f3);
        $filtro->adicionaFilho($f2);
        $esperado = array(4,3,5,6,16,116,8,18);
        $this->assertEquals(sort($esperado), sort($filtro->getListaIdsFilhos()));
    }
}