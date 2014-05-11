#!/bin/bash

# baixa o composer
php -r "readfile('https://getcomposer.org/installer');" | php

mkdir composer/

mv composer.phar composer/

cd composer

# cria o arquivo com as dependências
echo '
{
	"require": {
		"monolog/monolog": "1.0.*"
	}
}

{
	"require-dev": {
         "phpunit/phpunit": "4.1.*"
	}
}' > composer.json


# instala as dependências
php composer.phar install
