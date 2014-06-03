<?php

namespace classes_;

use PDO;
use PDOException;

class BDManager{

    /* @var $bd PDO */
    private $bd;

    public function __construct(){
        $this->conectar();
    }

    public function __destruct (){
        $this->desconectar();
    }


    public function conectar(){
        try{
	   $opcoes = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
	   $dados = parseConfigFile();
	   $dadosBd = $dados['bd'];
	   $this->bd = new PDO("$dadosBd[gerenciador]:host=$dadosBd[host];"
			     . "port=$dadosBd[porta];dbname=$dadosBd[nomeDoBanco]",
			     $dadosBd['usuario'], $dadosBd['senha'], $opcoes);
        }catch(PDOException $e){
	   exit ("Não foi possível se conectar com o banco de dados\n");
        }
    }
    
    //fecha a conexão
    public function desconectar(){
        $this->bd = null;
    }

    /**
     *	   Seleciona filtros que estão no banco de dados.
     * @param int $id
     * @param string $nome O nome do filtro a ser selecionado.
     * @param int $idPai (null por padrão)
     * @param boolean $pegarFilhos (falso por padrão) Se for verdadeiro, os objetos devolvidos
     *		      terão suas respectivas listas de filhos associados à eles.
     * @return FiltroCaso nenhum filtro seja encontrado, o valor null é devolvido.
     *		  Caso o $id ou o $nome seja diferente de null e um filtro com este id ou este nome 
     *		  for encontrado, um objeto do tipo Filtro é devolvido.
     *		  Em todos os outros casos, um vetor de Filtros é devolvido.
     */
    public function selecionaFiltro($id = null, $nome = null, $idPai = null, $pegarFilhos = false, $idAntigo = null){
        $cmd = 'SELECT * FROM filtros WHERE 1 = 1 ';
        $assoc = array();
        if ($idAntigo != null){
	   $cmd .= 'AND idAntigo = ? ';
	   $assoc[] = $idAntigo;
        }
        if ($id != null){
	   $cmd .= 'AND idFiltro = ? ';
	   $assoc[] = $id;
        }
        if ($nome != null){
	   $cmd .= 'AND filtro LIKE ? ';
	   $assoc[] = $nome;
        }
        if ($idPai !== null){
	   $cmd .= 'AND idFiltroPai = ? ';
	   $assoc[] = $idPai;
        }

        $st = $this->bd->prepare($cmd);
        if($st->execute($assoc)){
	   $resp = array();
	   while ($linha = $st->fetch(PDO::FETCH_ASSOC)){
	       $filtro = new Filtro($linha['idFiltro'], $linha['filtro'], $linha['idFiltroPai']);
	       // se os filhos também devem ser selescionados
	       if ($pegarFilhos){
		  // seleciona os filtros que têm este como pai
		  foreach($this->selecionaFiltro(null, null, $linha['idFiltro'], true) as $filho){
		      $filtro->adicionaFilho($filho);
		  }
	       }
	       $resp[$linha['idFiltro']] = $filtro;
	   }
	   // se o id ou o nome foi passado, o resultado obrigatoriamente tem
	   // apenas um Filtro, então, devolve-o.
	   if ($id != null || $nome != null){
	       return current($resp);
	   }
	   return $resp; // se não, devolve o vetor com os filtros
        }
        // se não encontrou nada.
        return null;
    }

    
    public function insereFiltro($nome, $idPai = null, $idAntigo = 0){
        $filtro = $this->selecionaFiltro(null, $nome);
        // se não existir um filtro com o nome passado, insere-o
        if ($filtro == null){
	   // só faz o inserte se o id do pai for nulo
	   // ou for um id que existe no banco
	   if ($idPai != null && count($this->selecionaFiltro($idPai))){
	       $cmd = "INSERT INTO filtros(filtro, idFiltroPai, idAntigo)"
		      . " VALUES (\"$nome\",$idPai,$idAntigo)";
	       // 1 é o número de linhas alteradas
	       return (1 == $this->bd->exec($cmd));
	   }else if($idPai == null){
	       $cmd = "INSERT INTO filtros(filtro, idFiltroPai, idAntigo)"
			. " VALUES (\"$nome\",NULL,$idAntigo)";
	       return (1 == $this->bd->exec($cmd));
	   }
	   return false; // se o idPai é diferente de null mas o pai não existe
        }
        return false;
    }
    
    private function selecionaAlternativas($idQuestao){
        $cmd = "SELECT idAlternativa, idQuestao, textoAlternativa, gabarito, letra"
	   . " FROM alternativa WHERE idQuestao = $idQuestao";
        $this->bd->query($cmd);
        $alternativas = array();
        foreach ($this->bd->query($cmd) as $alt){
	   $alternativa = new Alternativa($alt['idAlternativa'], 
			     $alt['textoAlternativa'], ($alt['gabarito'] == 1));
	   $alternativas[$alt['idAlternativa']] = $alternativa;
        }
        return $alternativas;
    }
    

    /**
     *  Busca por questões no banco de dados.
     *  
     * 
     * @param int $id
     * @param Filtro[] $filtros 
     * @param int $tipo Questao::QUESTAO_ALTERNATIVA ou Questao::QUESTAO_DISSERTATIVA
     * @return Caso nem o id nem o enunciado seja passado, um vetor de objetos 
     *	       é devolvido (ou um vetor vazio).
     */
    public function selecionaQuestao ($idInicial, $qntQuestoes, $filtros = array(), $tipo = null){
        $cmd = 'SELECT questao.idQuestao, questao.tipo,'
	   . ' questao.enunciado, questao.ano FROM questao';
        $assoc = array();
        if (is_array($filtros) && count($filtros) > 0){
	   $cont = 0;
	   foreach($filtros as $filtro){
	       $cmd .= " JOIN relacaoFiltroQuestao as R$cont ON"
		  . " R$cont.idQuestao = questao.idQuestao";
	       $inArg = '(';
	       $inArg .= implode(',', $filtro->getListaIdsFilhos());
	       $inArg .= ','.$filtro->getId();
	       $inArg .= ')';
	       $cmd .= " AND R$cont.idFiltro IN ".preg_replace('/\(,/', '(', $inArg);
	       $cont++;
	   }
        }
        $resp = array();
        $cmd = $cmd." AND $idInicial < questao.idQuestao ";
        if ($tipo !== null){
	   $cmd .= ' AND tipo = :tipo ';
	   $assoc[':tipo'] = $tipo;
        }
        
        $st = $this->bd->prepare($cmd." LIMIT $qntQuestoes");
        if($st->execute($assoc)){
	   while ($linha = $st->fetch(PDO::FETCH_ASSOC)){
	       if ($linha['tipo'] == Questao::QUESTAO_DISSERTATIVA){
		  $resp[$linha['idQuestao']] = new QuestaoDisserativa($linha['idQuestao'], $linha['enunciado'], $linha['ano']);
	       }else if ($linha['tipo'] == Questao::QUESTAO_ALTERNATIVA){
		  $q = new QuestaoTeste($linha['idQuestao'], $linha['enunciado'], $linha['ano']);
		  foreach($this->selecionaAlternativas($linha['idQuestao']) as $alt){
		      $q->adicionaAlternativa($alt);
		  }
		  $resp[$linha['idQuestao']] = $q;
	       }
	   }
        }
        return $resp;
    }

    /**
     *	   Associa todos os filtros (e seus filhos) recebidos à questão
     * cujo id foi passado.
     * @param int $idQuestao
     * @param Filtro[] $vetorFiltros
     * @return boolean
     */
    public function insereRelacaoQuestaoFiltros($idQuestao, $vetorFiltros) {
        $cmd = "INSERT INTO relacaoFiltroQuestao (idQuestao,idFiltro)"
			. " VALUES (:idQ,:idF)";
        $assoc = array();
        $st = $this->bd->prepare($cmd);
        $assoc[':idQ'] = $idQuestao;
        foreach($vetorFiltros as $filtro){
	   $assoc[':idF'] = $filtro->getId();
	   if ($st->execute($assoc)){
	       foreach ($filtro->getListaIdsFilhos() as $idF){
		  $assoc[':idF'] = $idF;
		  if (!$st->execute($assoc)){
		      return false;
		  }
	       }
	   }else{
	       return false;
	   }
        }
        return true;
    }
    
    
    private function insereAlternativa (Alternativa $alt, $idQuestao) {
        $cmd = "INSERT INTO alternativa(textoAlternativa,gabarito,idQuestao,letra)"
			. " VALUES (:texto,:gabarito, :idQ, :letra)";
        $assoc = array();
        $assoc[':texto'] = $alt->getTexto();
        $assoc[':gabarito'] = $alt->getEhCorreta();
        $assoc[':idQ'] = $idQuestao;
        $assoc[':letra'] = $alt->getLetra();
        
        $st = $this->bd->prepare($cmd);
        return $st->execute($assoc);
    }
    
    public function insereQuestaoTeste (QuestaoTeste $q, $idAntigo){
        $cmd = "INSERT INTO questao(idQuestao, enunciado, ano, tipo, idAntigo)"
			. " VALUES ($idAntigo, :enunciado,:ano, :tipo, :idAntigo)";
        $assoc = array();
        $assoc[':enunciado'] = $q->getEnunciado();
        $assoc[':ano'] = $q->getAno();
        $assoc[':tipo'] = Questao::QUESTAO_ALTERNATIVA;
        $assoc[':idAntigo'] = $idAntigo;
        
        // Começa uma transição (caso algo falhe, cancela tudo)
        $this->bd->beginTransaction();
        
        $st = $this->bd->prepare($cmd);

        if($st->execute($assoc)){
	   while ($alt = $q->proximaAlternativa()){
	       // se alguma alternativa não seja inserida corretamente
	       if (!$this->insereAlternativa($alt, $q->getId())){
		  $this->bd->rollBack(); // desfaz todas as inserções (da questao e das alternativas)
		  return false;
	       }
	   }
	   $this->bd->commit();
	   $this->insereRelacaoQuestaoFiltros($q->getId(), $q->getFiltro());
	   return true;
        }
        return false;
    }
    
    public function insereQuestaoDissertativa (QuestaoDisserativa $q, $idAntigo){
        $cmd = "INSERT INTO questao(idQuestao, enunciado, ano, tipo, idAntigo)"
			. " VALUES ($idAntigo, :enunciado,:ano,:tipo,:idAntigo)";
        $assoc = array();
        $assoc[':enunciado'] = $q->getEnunciado();
        $assoc[':ano'] = $q->getAno();
        $assoc[':tipo'] = Questao::QUESTAO_DISSERTATIVA;
        $assoc[':idAntigo'] = $idAntigo;
        $st = $this->bd->prepare($cmd);
        if ($st->execute($assoc)){
	   $this->insereRelacaoQuestaoFiltros($q->getId(), $q->getFiltro());
	   return true;
        }
        return false;
    }

}