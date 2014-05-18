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
    public function selecionaFiltro($id = null, $nome = null, $idPai = null, $pegarFilhos = false){
        $cmd = 'SELECT * FROM filtros WHERE 1 = 1 ';
        $assoc = array();
        if ($id != null){
	        $cmd .= 'AND id_filtro = :id ';
	        $assoc[':id'] = $id;
        }
        if ($nome != null){
	        $cmd .= 'AND filtro = :nome ';
	        $assoc[':nome'] = $nome;
        }
        if ($idPai != null){
	        $cmd .= 'AND id_filtro_pai = :idPai ';
	        $assoc[':idPai'] = $idPai;
        }

        $st = $this->bd->prepare($cmd);

        if($st->execute($assoc)){
	   $resp = array();
	   while ($linha = $st->fetch(PDO::FETCH_ASSOC)){
	       $filtro = new Filtro($linha['id_filtro'], $linha['filtro'], $linha['id_filtro_pai']);
	       // se os filhos também devem ser selescionados
	       if ($pegarFilhos){
		       // seleciona os filtros que têm este como pai
		       foreach($this->selecionaFiltro(null, null, $linha['id_filtro'], $pegarFilhos) as $filho){
			       $filtro->adicionaFilho($filho);
		       }
	       }
	       $resp[$linha['id_filtro']] = $filtro;
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

    
    public function insereFiltro($nome, $idPai = null){
        $filtro = $this->selecionaFiltro(null, $nome);
        // se não existir um filtro com o nome passado, insere-o
        if ($filtro == null){
	   // só faz o inserte se o id do pai for nulo
	   // ou for um id que existe no banco
	   if ($idPai != null && count($this->selecionaFiltro($idPai))){
	       $cmd = "INSERT INTO filtros(filtro, id_filtro_pai, id_antigo)"
		      . " VALUES (\"$nome\",$idPai)";
	       // 1 é o número de linhas alteradas
	       return (1 == $this->bd->exec($cmd));
	   }else if($idPai == null){
	       $cmd = "INSERT INTO filtros(filtro, id_filtro_pai, id_antigo)"
			. " VALUES (\"$nome\",NULL)";
	       
	       return (1 == $this->bd->exec($cmd));
	   }
	   return false; // se o idPai é diferente de null mas o pai não existe
        }
        return false;
    }
    
    private function selecionaAlternativas($idQuestao){
        $cmd = "SELECT id_alternativa, id_questao, texto_alternativa, gabarito, letra"
	   . " FROM alternativa WHERE id_questao = $idQuestao";
        // TODO: percorrer o retorno do select e criar o vetor com as alternativas
        $this->bd->query($cmd);
        $alternativas = array();
        foreach ($this->bd->query($cmd) as $alt){
	   $alternativas[] = array('idAlternativa' => $alt['idAlternativa'],
				'idQuestao' => $alt['idQuestao'],
				'texto' => $alt['textoAlternativa'],
				'gabarito' => ($alt['gabarito'] == 1));
        }
        return $alternativas;
    }
    

    /**
     *  Busca por questões no banco de dados.
     *  
     * 
     * @param int $id
     * @param Filtro[] $filtros
     * @param String $enunciado
     * @param int $tipo Questao::QUESTAO_ALTERNATIVA ou Questao::QUESTAO_DISSERTATIVA
     * @return Se for passado o id ou o enunciado, é devolvido um objeto do tipo QuestaoDissertativa
     *  ou QuestaoAlternativa, ou ainda null (se não for encontrado).
     *	       Caso nem o id nem o enunciado seja passado, um vetor de objetos 
     *	       é devolvido (ou um vetor vazio).
     */
    public function selecionaQuestao ($id = null, $filtros = array(), $enunciado = null, $tipo = null){
        $cmd = 'SELECT * FROM questao WHERE 1 = 1 ';
        $assoc = array();
        if ($id != null){
	   $cmd .= 'AND idQuestao = :id ';
	   $assoc[':id'] = $id;
        }
        
        if ($enunciado != null){
	   $cmd .= 'AND enunciado = :enun ';
	   $assoc[':enun'] = $enunciado;
        }
        if ($tipo !== null){
	   $cmd .= 'AND tipo = :tipo ';
	   $assoc[':tipo'] = $tipo;
        }
        if (is_array($filtros) && count($filtros) > 0){
	   $inArg = '(';
	   foreach($filtros as $filtro){
	       $inArg .= implode(',', $filtro->getListaIdsFilhos());
	       $inArg .= ','.$filtro->getId();
	   }
	   $inArg .= ')';
	   $cmd .= " AND IN $inArg ";
        }
        
        $st = $this->bd->prepare($cmd);
        if($st->execute($assoc)){
	   $resp = array();
	   while ($linha = $st->fetch(PDO::FETCH_ASSOC)){
	       if ($linha['tipo'] == Questao::QUESTAO_DISSERTATIVA){
		  $resp[] = new QuestaoDisserativa($linha['idQuestao'], $linha['enunciado'], $linha['ano']);

	       }else if ($linha['tipo'] == Questao::QUESTAO_ALTERNATIVA){
		  $q = new QuestaoTeste($linha['idQuesta'], $linha['enunciado'], $linha['ano']);
		  foreach($this->selecionaAlternativas($linha['idQuestao']) as $alt){
		      $q->adicionaAlternativa($alt['texto'], $alt['gabarito']);
		  }
	       }
	   }
        }
        
        if ($id != null || $enunciado != null){
	   if (count($resp) > 0){
	       return current($resp);
	   }
	   return null;
        }
        return $resp;
    }
    
    public function insereQuestaoDissertativa (QuestaoDisserativa $q, $idAntigo){
        $cmd = "INSERT INTO questao(enunciado, ano, tipo, id_antigo)"
			. " VALUES (\":enunciado\",:ano, :tipo, :id_antigo)";
        $assoc = array();
        $assoc[':enunciado'] = $q->getEnunciado();
        $assoc[':ano'] = $q->getAno();
        $assoc[':tipo'] = Questao::QUESTAO_DISSERTATIVA;
        $assoc[':id_antigo'] = $idAntigo;
        $st = $this->bd->prepare($cmd);

        return $st->execute($assoc);
    }
    
}
