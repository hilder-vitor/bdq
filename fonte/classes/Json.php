<?php
namespace classes_;


class Json {

    public static function devolveJsonFiltrosFilhos($idFiltroPai = null, $nome = null){
        $bd = new BDManager();
        if ($nome != null){
	   $pai = $bd->selecionaFiltro(null, $nome);
	   if($pai != null){
	       $filtros = $bd->selecionaFiltro(null, null, $pai->getId(),false);
	   }else{
	       $filtros = array();
	   }
        }else{
	   $filtros = $bd->selecionaFiltro(null, null, $idFiltroPai,false);
        }
        $jsonFiltros = array();
        foreach($filtros as $filtro){
	   $jsonFiltros[] = Json::transformaFiltroEmArray($filtro);
        }
        return json_encode(array('status' => 0,
			 'msg' => 'OK',
			 'filtros' => $jsonFiltros));
    }

    public static function transformaFiltroEmArray(Filtro $filtro){
        return array('idFiltro' => $filtro->getId(),
		  'nome' => $filtro->getNome(),
		  'idPai' => $filtro->getIdPai());
    }
}
