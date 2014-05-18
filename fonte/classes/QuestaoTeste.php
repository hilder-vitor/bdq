<?php

namespace classes_;


class QuestaoTeste extends Questao {
    // vetor de alternativas
    private $alternativas = array();
    
    // usado no método proximaAlternativa
    private $indiceProximaAlternativa = 0;
    
    public function getAlternativas() {
        return $this->alternativas;
    }
    
    /**
     *	 Devolve um string representando uma alternativa ou null caso já tenha
     * devolvido todas as alternativas (ou caso não haja alternativas)
     *	 Na primeira vez que é chamado, devolve a primeira alternativa,
     * na segunda vez, devolve a segunda, e assim sucessivamente.
     */
    public function proximaAlternativa(){
        if ($this->indiceProximaAlternativa < count($this->alternativas)){
	   return $this->alternativas[$this->indiceProximaAlternativa++];
        }
        return null;
    }

    /**
     *   Associa uma alternativa à questão.
     * 
     * @param String $alternativa Texto da alternativa que vai ser associada à questão.
     * @param boolean $correta (falso por padrão) Se for verdadeiro, então esta alternativa
     *		      será marcada como correta.
     */
    public function adicionaAlternativa(Alternativa $alternativa) {
        $this->alternativas[] = $alternativa;
    }


}
