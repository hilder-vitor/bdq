<?php

$raiz = '/var/www/bancoDeQuestoes/';

function autoload($Class) {
    global $raiz;

    $Class = str_replace( __NAMESPACE__.'\\', '', $Class );
    $Class = str_replace(array('\\','/','_' ), DIRECTORY_SEPARATOR, $raiz.DIRECTORY_SEPARATOR.'fonte'.DIRECTORY_SEPARATOR.$Class.'.php' );

    if( false === ( $Class = realpath( $Class ) ) ) {
	return false;
    } else {
       require_once( $Class );
       return true;
    }
}

/** Função global para parsear o arquivo de configuração */
function parseConfigFile (){
    global $raiz;
    return parse_ini_file("$raiz/recursos/config.ini", true);
}

spl_autoload_register('autoload');