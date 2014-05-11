#!/bin/bash

#cd testes/

# REPLICA O BANCO DE DADOS TESTE
nome="bancoTeste$RANDOM";
echo "CREATE DATABASE $nome;" > $nome;
echo "USE $nome" >> $nome;
cat bancoTeste >> $nome;
usuario=$(cat ../recursos/config.ini  | fgrep usuario | cut -d= -f2 | sed 's/^ *//')
senha=$(cat ../recursos/config.ini  | fgrep senha | cut -d= -f2 | sed 's/^ *//')
# executa o comando mysql para criar o banco
mysql -u$usuario -p$senha -e "source $nome";

# pega o nome do banco de dados que está sendo usado
nomeAtual=$(cat ../recursos/config.ini  | fgrep nomeDoBanco | cut -d= -f2 | sed 's/^ *//')

cp ../recursos/config.ini configBKP.ini # backup do arquivo de configuração
# Altera o nome do banco no arquivo de configuração original
cat configBKP.ini | sed "s/$nomeAtual/$nome/" > ../recursos/config.ini


phpunit --colors .

# apaga o arquivo criado
rm $nome;
# deleta a base de dados criada
mysql -u$usuario -p$senha -e "DROP DATABASE $nome";

# Restaura o arquivo de configuração
mv configBKP.ini ../recursos/config.ini

#cd ../
