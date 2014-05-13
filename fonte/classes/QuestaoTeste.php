<?php

namespace classes_;


class QuestaoTeste extends Questao {
    // vetor com os textos das alternativas
    private $alternativas = array();
    // inteiro entre 0 e o tamanho do vetor $alternativas
    private $indiceAlternativaCorreta = null;
    
    public function getAlternativas() {
        return $this->alternativas;
    }

    public function getIndiceAlternativaCorreta() {
        return $this->indiceAlternativaCorreta;
    }

    /**
     *   Associa uma alternativa à questão.
     * 
     * @param String $alternativa Texto da alternativa que vai ser associada à questão.
     * @param boolean $correta (falso por padrão) Se for verdadeiro, então esta alternativa
     *		      será marcada como correta.
     */
    public function adicionaAlternativa($alternativa, $correta = false) {
        $this->alternativas[] = $alternativa;
        if ($correta){
	   $this->indiceAlternativaCorreta = (count($this->alternativas) - 1);
        }
    }


}
