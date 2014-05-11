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

}