<?php

namespace classes_;

class Filtro {

    private $id;	 // o id do filtro
    private $nome;	 // o nome do filtro (por exemplo "Biologia")
    private $idPai;	 // id do filtro pai
    private $filhos;	 // um array de filtros

    public function __construct ($id, $nome, $idPai = null){
	    $this->id = $id;
	    $this->nome = $nome;
	    $this->filhos = array();
	    $this->idPai = $idPai;
    }

    public function getNome(){
        return $this->nome;
    }

    public function adicionaFilho (Filtro $filho){
	    $this->filhos[$filho->getId()] = $filho;
    }

    public function getFilho ($id = null){
	    if ($id == null){
		    return $this->filhos;
	    }

	    if (isset($this->filhos[$id])){
		    return $this->filhos[$id];
	    }
	    return null;
    }

    public function getId(){
	    return $this->id;
    }

    public function getIdPai(){
        return $this->idPai;
    }

    public function setPai($idPai){
        if ($idPai > 0){
	  $this->idPai = $idPai;
        }
    }
    
    private function getListaIdsFilhosRec($vetFilhos){
        $ids = array_keys($vetFilhos);
        foreach ($vetFilhos as $filtro){
	  $idsFilhos = $this->getListaIdsFilhosRec($filtro->getFilho());
	  $ids = array_merge($ids, $idsFilhos);
        }
        return $ids;
    }
    
    public function getListaIdsFilhos(){
        return $this->getListaIdsFilhosRec($this->filhos);
    }
}
