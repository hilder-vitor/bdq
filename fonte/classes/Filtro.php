<?php

namespace classes_;

class Filtro {

	private $id;	 // o id do filtro
	private $nome;	 // o nome do filtro (por exemplo "Biologia")
	private $filhos; // um array de filtros


	public function __construct ($id, $nome){
		$this->id = $id;
		$this->nome = $nome;
		$this->filhos = array();
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

}
