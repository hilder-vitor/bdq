<?php

require_once '../autoloader.php';

use classes_\QuestaoDisserativa as QuestaoDisserativa;
use classes_\BDManager as BDManager;


class BDManagerTest extends PHPUnit_Framework_TestCase
{
    
    public function testInsereQuestaoDissertativa(){
        $bd = new BDManager();
        $q = new QuestaoDisserativa(3, 'Quem é que foi que descobriu o brasil?', 2013);
        $this->assertTrue($bd->insereQuestaoDissertativa($q, 13));
    }
    
    public function testInsereFiltro()
    {
        $bd = new BDManager();
        $nome = 'Physique';
        $this->assertTrue($bd->insereFiltro($nome));
        $filtro = $bd->selecionaFiltro(null, $nome);
        $this->assertTrue($bd->insereFiltro('Fluyds', $filtro->getId()));
        $this->assertFalse($bd->insereFiltro($nome));
    }
    
    public function testSelecionaFiltro(){
        $bd = new BDManager();
        $bio = $bd->selecionaFiltro(null,'Biologia');
        $this->assertEquals('Biologia', $bio->getNome());
        $fisica = $bd->selecionaFiltro(null,'Física');
        $this->assertEquals('Física', $fisica->getNome());
        $mat = $bd->selecionaFiltro(null,'Matemática');
        $this->assertEquals('Matemática', $mat->getNome());
        $this->assertEquals('Matemática', $bd->selecionaFiltro(895)->getNome());
        // seleciona pelo id do pai
        $filhosMat = $bd->selecionaFiltro(null, null, 895);
        $this->assertEquals(36, count($filhosMat));
        $comb = current($filhosMat);
        $this->assertEquals('Análise Combinatória', $comb->getNome());
    }

}
