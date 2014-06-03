<?php

function pegaUnidade ($enunciado){
    $unidade = array();
    if (preg_match('/<p>\s*\(([a-zA-Z][a-zA-Z0-9]([a-zA-Z]-?)*)\s*[0-9]*\s*\)/', $enunciado, $unidade)){
        return $unidade[1];
    }
}

function converteParaBase64 ($arq){
    $type = pathinfo($arq[0], PATHINFO_EXTENSION);
    $data = file_get_contents($arq[0]);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

function converteImagens ($enunciado){
    $padrao = '/<img src="..\/imagens\/questao\/[a-zA-Z]+\/[0-9]+\.[a-zA-Z]+" \/>/';
    if (preg_match($padrao, $enunciado)){
        $p = '/..\/imagens\/questao\/[a-zA-Z]+\/[0-9]+\.[a-zA-Z]+/';
        return preg_replace_callback($p, "converteParaBase64", $enunciado);
    }
    return $enunciado;
}

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
require_once('/var/www/bdq/fonte/classes/BDManager.php');

$conexao = mysql_pconnect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR);
mysql_select_db($database, $conexao);





//-------------
// seleciona as disciplinas
$disciplinas = array();
$rsDisc = mysql_query('SELECT * FROM cad_disciplina', $conexao);
while ($lnDisc = mysql_fetch_assoc($rsDisc)){
    $disciplinas[$lnDisc['id_disciplina']] = $lnDisc['disciplina'];
}
// ------------------

// seleciona as questões
$sqlQuestao = 'SELECT * FROM questao WHERE id_professor = 0';

$rsQuestao = mysql_query ($sqlQuestao, $conexao) or die(mysql_error());

use classes_\BDManager as BDManager;
use classes_\QuestaoTeste as QuestaoTeste;
use classes_\QuestaoDisserativa as QuestaoDisserativa;
use classes_\Questao as Questao;


$bdq = new BDManager();

$total = 0;
$comVest = 0;
while ($dados = mysql_fetch_assoc($rsQuestao)){
    
    $enunciado = arrumaAcento($dados['texto']);
    $enunciado = converteImagens($enunciado);
    $unidade = pegaUnidade($enunciado);
    // se conseguiu achar a unidade
    if ($unidade != ''){
        
        $comVest++;
        
        $idQuestao = $dados["id_questao"]; // relacionado com id_antigo na tabela filtros
        $idDisciplina = $dados["id_disciplina"];
        $idFiltro = $dados['filtro_num'];
        $tipo = $dados['tipo']; // alternativa ou dissertativa
        $ano = $dados['ano'];
        
        if ($tipo == Questao::QUESTAO_ALTERNATIVA){
	   $questao = new QuestaoTeste($idQuestao, $enunciado, $ano);
	   // seleciona as alternativas
	   $sqlAlt = "SELECT * FROM alternativa WHERE id_questao = $idQuestao";
	   $rsAlt = mysql_query($sqlAlt, $conexao);
	   while ($lnAlt = mysql_fetch_assoc($rsAlt)){
	       $questao->adicionaAlternativa(new classes_\Alternativa($lnAlt['id_alternativa'], $lnAlt['texto_alternativa'], $lnAlt['gabarito'], $lnAlt['letra']));
	   }
        }else{
	   $questao = new QuestaoDisserativa($idQuestao, $enunciado, $ano);
        }
        $filtro = current($bdq->selecionaFiltro(null, null, null, false, $idFiltro));
        if ($filtro != false){
	   $questao->adicionaFiltro($filtro);
        }
        
        // -----------------------------------------
        //      Adiciona a unidade como filtro
        // -----------------------------------------
        if (strcasecmp($unidade,'PUC-Rio') == 0){
	   $unidade = 'PUC-Rio';
        }else if (strcasecmp($unidade,'Unioeste') == 0){
	   $unidade = 'Unioeste';
        }else if(strcasecmp('Unirio',$unidade) == 0){
	   $unidade = 'Unirio';
        }else if(strcasecmp($unidade ,'Unitau') == 0){
	   $unidade = 'Unitau';
        }else if (strcasecmp($unidade , 'Mackenzie') == 0){
	   $unidade = 'Mackenzie';
        }else{
	   $unidade = strtoupper($unidade);
        }
        
        $filtro = $bdq->selecionaFiltro(null, $unidade);
        if ($filtro != false){
	   $questao->adicionaFiltro($filtro);
        }
        if ($tipo == Questao::QUESTAO_ALTERNATIVA){
	   $bdq->insereQuestaoTeste($questao, $idQuestao);
        }else{
	   $bdq->insereQuestaoDissertativa($questao, $idQuestao);
        }
        if($comVest % 15 == 0){
	   echo "$comVest\n";
        }
    }

    $total++;
    if ($total % 1000 == 0){
        var_dump($questao);
    }
}

echo "QNT COM VEST: $comVest\n";
echo "TOTAL: $total\n";