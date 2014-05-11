<?php

require_once "../fonte/Filtro.class.php";

class FiltroTest extends PHPUnit_Framework_TestCase
{
    
    public function testGetId()
    {
        $filtro = new Filtro(4, 'Matemática');
     
        $this->assertEquals(5, $filtro->getId());
        $this->assertEquals(4, $filtro->getId());
    }

}