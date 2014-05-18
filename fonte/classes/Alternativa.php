<?php

namespace classes_;


class Alternativa {
    
    private $id;
    private $texto;
    private $ehCorreta;
    
    public function __construct($id, $texto, $ehCorreta = false) {
        $this->setId($id);
        $this->setTexto($texto);
        $this->setEhCorreta($ehCorreta);
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

    
}
