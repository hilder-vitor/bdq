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
    
    public static function transformaVetorDeQuestoesEmJson($questoes){
        $vetor = array();
        foreach ($questoes as $questao){
	   $vetor[] = Json::transformaQuestaoEmArray($questao);
        }
        $resp = array();
        $resp['status'] = 0;
        $resp['msg'] = 'OK';
        $resp['questoes'] = $vetor;
        return json_encode($resp);
    }
    
    public static function transformaQuestaoEmArray(Questao $questao){
        if ($questao->getTipo() == Questao::QUESTAO_ALTERNATIVA){
	   return Json::transformaQuestaoTesteEmArray($questao);
        }
        return Json::transformaQuestaoDissertativaEmArray($questao);
    }
    
    public static function transformaQuestaoDissertativaEmArray(QuestaoDisserativa $questao){
        return array('id' => $questao->getId(),
		  'enunciado' => $questao->getEnunciado(),
		  'tipo' => $questao->getTipo(),
		  'ano' => $questao->getAno());
    }
    
    public static function transformaQuestaoTesteEmArray(QuestaoTeste $questao){
        $alt = array();
        foreach($questao->getAlternativas() as $alternativa){
	   $alt[] = Json::transformaAlternativaEmArray($alternativa);
        }
        return array('id' => $questao->getId(),
		  'enunciado' => $questao->getEnunciado(),
		  'tipo' => $questao->getTipo(),
		  'ano' => $questao->getAno(),
		  'alternativas' => $alt);
    }
    
    public static function transformaAlternativaEmArray(Alternativa $alt){
        return array('idAlternativa' => $alt->getId(),
		   'texto' => $alt->getTexto(),
		   'letra' => $alt->getLetra(),
		   'ehCorreta' => $alt->getEhCorreta());
    }
   
}
