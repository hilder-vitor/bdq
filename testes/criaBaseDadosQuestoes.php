<?php

function arrumaAcento ($disciplina){
		$disciplina = preg_replace('/\&Aacute;/', 'Á', $disciplina);
		$disciplina = preg_replace('/\&aacute;/', 'á', $disciplina);
		$disciplina = preg_replace('/\&Acirc;/', 'Â', $disciplina);
		$disciplina = preg_replace('/\&acirc;/', 'â', $disciplina);
		$disciplina = preg_replace('/\&Agrave;/', 'À', $disciplina);
		$disciplina = preg_replace('/\&agrave;/', 'à', $disciplina);
		$disciplina = preg_replace('/\&Aring;/', 'Å', $disciplina);
		$disciplina = preg_replace('/\&aring;/', 'å', $disciplina);
		$disciplina = preg_replace('/\&Atilde;/', 'Ã', $disciplina);
		$disciplina = preg_replace('/\&atilde;/', 'ã', $disciplina);
		$disciplina = preg_replace('/\&Eacute;/', 'É', $disciplina);
		$disciplina = preg_replace('/\&eacute;/', 'é', $disciplina);
		$disciplina = preg_replace('/\&Ecirc;/', 'Ê', $disciplina);
		$disciplina = preg_replace('/\&ecirc;/', 'ê', $disciplina);
		$disciplina = preg_replace('/\&Egrave;/', 'È', $disciplina);
		$disciplina = preg_replace('/\&egrave;/', 'è', $disciplina);
		$disciplina = preg_replace('/\&Iacute;/', 'Í', $disciplina);
		$disciplina = preg_replace('/\&iacute;/', 'í', $disciplina);
		$disciplina = preg_replace('/\&Icirc;/', 'Î', $disciplina);
		$disciplina = preg_replace('/\&icirc;/', 'î', $disciplina);
		$disciplina = preg_replace('/\&Igrave;/', 'Ì', $disciplina);
		$disciplina = preg_replace('/\&igrave;/', 'ì', $disciplina);
		$disciplina = preg_replace('/\&Oacute;/', 'Ó', $disciplina);
		$disciplina = preg_replace('/\&oacute;/', 'ó', $disciplina);
		$disciplina = preg_replace('/\&Ocirc;/', 'Ô', $disciplina);
		$disciplina = preg_replace('/\&ocirc;/', 'ô', $disciplina);
		$disciplina = preg_replace('/\&Ograve;/', 'Ò', $disciplina);
		$disciplina = preg_replace('/\&ograve;/', 'ò', $disciplina);
		$disciplina = preg_replace('/\&Otilde;/', 'Õ', $disciplina);
		$disciplina = preg_replace('/\&otilde;/', 'õ', $disciplina);
		$disciplina = preg_replace('/\&Uacute;/', 'Ú', $disciplina);
		$disciplina = preg_replace('/\&uacute;/', 'ú', $disciplina);
		$disciplina = preg_replace('/\&Ucirc;/', 'Û', $disciplina);
		$disciplina = preg_replace('/\&ucirc;/', 'û', $disciplina);
		$disciplina = preg_replace('/\&Ugrave;/', 'Ù', $disciplina);
		$disciplina = preg_replace('/\&ugrave;/', 'ù', $disciplina);
		$disciplina = preg_replace('/\&Uuml;/', 'Ü', $disciplina);
		$disciplina = preg_replace('/\&uuml;/', 'ü', $disciplina);
		$disciplina = preg_replace('/\&Ccedil;/', 'Ç', $disciplina);
		$disciplina = preg_replace('/\&ccedil;/', 'ç', $disciplina);
		$disciplina = preg_replace('/\&amp;/', '&', $disciplina);
		$disciplina = preg_replace('/\&quot;/', "'", $disciplina);
		$disciplina = preg_replace('/\&acute;/', "'", $disciplina);

		return $disciplina;
}




include('../autoloader.php');

include ('/var/www/va/Connections/include_dados_conexao.php');
require_once('/var/www/bancoDeQuestoes/fonte/classes/BDManager.php');

$conexao = mysql_pconnect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_select_db($database, $conexao);


$nomeSenha = array();

$sqlFiltors = 'SELECT * FROM filtro_questao';

$rsFiltros = mysql_query ($sqlFiltors, $conexao) or die(mysql_error());

use classes_\BDManager as BDManager;
$bdq = new BDManager();

$filtrosDisciplina = array();


$k = 1;
while ($dados = mysql_fetch_assoc($rsFiltros)){
        $idFiltro = $dados['id_filtro'];
        $idDisciplina = $dados["id_disciplina"];
        
        $disciplina = arrumaAcento($dados["disciplina"]);
        
        $bdq->insereFiltro($disciplina);
        
        if (!isset($filtrosDisciplina[$idDisciplina])){
	   $filtrosDisciplina[$idDisciplina] = $bdq->selecionaFiltro(null, $disciplina);
	   var_dump($filtrosDisciplina[$idDisciplina]);
        }

	for($i = 1; $i < 4; $i++){
	   if (trim($dados["nivel_$i"]) != ''){
	       $nivel = arrumaAcento($dados["nivel_$i"]);
	       if ($i == 1){
		  $pai = $filtrosDisciplina[$idDisciplina];
	       }else{
		  $pai = $bdq->selecionaFiltro(null, arrumaAcento($dados['nivel_'.($i-1)]));
	       }
	       $bdq->insereFiltro($nivel, ($pai == null ? null : $pai->getId()));
	   }
	}
	//echo $k++;
	//echo "\n";
}


