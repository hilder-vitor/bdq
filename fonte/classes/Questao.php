<?php
namespace classes_;

class Questao {

    /* definindo constanstes para os tipos de questão */
    const QUESTAO_ALTERNATIVA = 1;
    const QUESTAO_DISSERTATIVA = 2;

    protected $id;
    protected $enunciado;
    protected $ano;
    /* vetor dos filtros associados à questão
     *  indexados pelo id do filtro
     *  (guardando apenas o 1º nível. Os filhos estão nos vetores de
     *  filhos dos próprios filtros)
     */
    protected $filtros;
    
    public function __construct($id, $enunciado, $ano) {
        $this->ano = $ano;
        $this->enunciado = $enunciado;
        $this->id = $id;
    }
    
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
    
    private function adicionaFiltroRec(Filtro $f, $filtros){
        if (isset($filtros[$f->getId()])){
	   return true;
        }
        // se o filtro passado for filho de algum filtro
        if (isset($filtros[$f->getIdPai()])){
	   $filtro = $filtros[$f->getIdPai()];
	   $filtro->adicionaFilho($f);
	   return true;
        }
        // tenta adicionar o filtro na sub-árvore a partir de algum filtro
        foreach ($filtros as $filtro){
	   if ($this->adicionaFiltroRec($f, $filtro->getFilho())){
	       return true;
	   }
        }
        return false;
    }
    
    public function adicionaFiltro(Filtro $f){
        // tenta adicionar o filtro como filho de algum filtro já existente
        $adicionou = $this->adicionaFiltroRec($f, $this->filtros);
        if (!$adicionou){ // se não adicionou
	   $this->filtros[$f->getId()] = $f;
        }
    }
}