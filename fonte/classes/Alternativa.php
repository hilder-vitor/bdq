<?php

namespace classes_;


class Alternativa {
    
    private $id;
    private $texto;
    private $letra = null;
    private $ehCorreta;
    
    public function __construct($id, $texto, $ehCorreta = false, $letra = null) {
        $this->setId($id);
        $this->setTexto($texto);
        $this->setEhCorreta($ehCorreta);
        $this->setLetra($letra);
    }
    
    
    public function getId() {
        return $this->id;
    }

    public function getTexto() {
        return $this->texto;
    }

    public function getEhCorreta() {
        return $this->ehCorreta;
    }

    public function getLetra(){
        return $this->letra;
    }
    
    public function setId($id) {
        if ($id > 0){
	   $this->id = $id;
        }else{
	   $this->id = null;
        }
    }

    public function setTexto($texto) {
        $this->texto = $texto;
    }

    public function setEhCorreta($ehCorreta) {
        if ($ehCorreta){
	   $this->ehCorreta = true;
        }else{
	   $this->ehCorreta = false;
        }
    }

    public function setLetra($l){
        if ($l >= 'a' && $l <= 'z'){
	   $this->letra = $l;
        }
    }
    
}
