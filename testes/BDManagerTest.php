<?php

require_once '../autoloader.php';

use classes_\Alternativa as Alternativa;
use classes_\BDManager as BDManager;
use classes_\QuestaoDisserativa as QuestaoDisserativa;
use classes_\QuestaoTeste as QuestaoTeste;


class BDManagerTest extends PHPUnit_Framework_TestCase
{
    
    public function testInsereQuestaoDissertativa(){
        $bd = new BDManager();
        $q = new QuestaoDisserativa(1234598763, 'Quem que descobriu o Brasil?', 2013);
        $this->assertTrue($bd->insereQuestaoDissertativa($q, 13));
    }
    
    public function testInsereQuestaoTeste(){
        $bd = new BDManager();
        $q = new QuestaoTeste(1234598763, 'Quem que descobriu o Brasil?', 2013);
        $q->adicionaAlternativa(new Alternativa(3, 'Pedro Pedreiro'));
        $q->adicionaAlternativa(new Alternativa(4, 'Gil Brother'));
        $q->adicionaAlternativa(new Alternativa(2, 'Pedro Alváres Cabral', true));
        $q->adicionaAlternativa(new Alternativa(7, 'Cabraliz'));
        $this->assertTrue($bd->insereQuestaoTeste($q, 13));
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
