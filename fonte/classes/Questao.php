<?php
namespace classes_;

class Questao {
    
    protected $id;
    protected $enunciado;
    protected $ano;
    
    public function getId() {
        return $this->id;
    }

    public function getEnunciado() {
        return $this->enunciado;
    }

    public function getAno() {
        return $this->ano;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setEnunciado($enunciado) {
        $this->enunciado = $enunciado;
    }

    public function setAno($ano) {
        $this->ano = $ano;
    }

}