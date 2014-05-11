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

    public function setAlternativas($alternativas) {
        $this->alternativas = $alternativas;
    }

    public function setIndiceAlternativaCorreta($k) {
        if (isset($this->alternativas[$k])){
	   $this->indiceAlternativaCorreta = $k;
        }
    }

}
