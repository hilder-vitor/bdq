<?php

namespace classes_;

use classes_\Filtro;
use PDO;
use PDOException;

class BDManager{

    private $bd;

    public function __construct(){
        $this->conectar();
    }

    public function __destruct (){
        $this->desconectar();
    }


    public function conectar(){
        try{
	   $dados = parseConfigFile();
	   $dadosBd = $dados['bd'];
	   $this->bd = new PDO("$dadosBd[gerenciador]:host=$dadosBd[host];port=$dadosBd[porta];dbname=$dadosBd[nomeDoBanco]", $dadosBd['usuario'], $dadosBd['senha']);
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
		       foreach($this->selecionaFiltro(null, null, $linha['id_filtro']) as $filho){
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
	       $cmd = "INSERT INTO filtros(filtro, id_filtro_pai)"
		      . " VALUES ('$nome',$idPai)";
	       // 1 é o número de linhas alteradas
	       return (1 == $this->bd->exec($cmd));
	   }else if($idPai == null){
	       $cmd = "INSERT INTO filtros(filtro, id_filtro_pai)"
			. " VALUES ('$nome',NULL)";
	       return (1 == $this->bd->exec($cmd));
	   }
	   return false; // se o idPai é diferente de null mas o pai não existe
        }
        return false;
    }

}
