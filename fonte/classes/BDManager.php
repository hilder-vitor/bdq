<?php

require_once 'Filtro.class.php';

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
	    $this->bd = new PDO('mysql:host=localhost;port=3306;dbname=bancoDeQuestoes', 'root', '8Ywb9OSsys8uWXx6mz8egNkRQNxAT23a');
        }catch(PDOException $e){
		    exit ("Não foi possível se conectar com o banco de dados\n");
        }
    }
    
    //fecha a conexão
    public function desconectar(){
        $this->bd = null;
    }



    public function selecionaFiltro($id = null, $nome = null, $idPai = null, $pegarFilhos = true){
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

	    echo $cmd."$idPai\n";

	    $st = $this->bd->prepare($cmd);

	    $resp = array();

	    if($st->execute($assoc)){
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
	    }

	    return $resp;
    }


}


?>
