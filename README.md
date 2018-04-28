# SALIC

[![Junte-se ao nosso chat no https://gitter.im/salic-minc/Lobby](https://badges.gitter.im/salic-minc/Lobby.svg)](https://gitter.im/salic-minc/Lobby?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Bem vindo/a à documentação do SALIC! Aqui você vai encontrar diversas documentações sobre o processo de desenvolvimento do SALIC, versionameno e publicação.


## Passos iniciais:

- ```composer update```
- ```npm install```

## Docker
Utilizamos o Docker como plataforma de desenvolvimento com o intuito de garantir o mesmo ambiente de desenvolvimento 
independentemente do Sistema Operacional(SO) utilizado. Informaçoes mais detalhadas sobre a utilização do docker clique
[aqui](doc/Guia_utilizacao_docker.md).

Para criar um ambiente para trabalhar com o SALIC basta executar o comando abaixo:
```
  docker-compose up -d
```

Para parar o container basta digitar:
```
  docker-compose stop
```

## Submodulos
* Esse projeto contem os manuais e implementações de layout do salic [Layout](https://github.com/culturagovbr/salic-minc-layout)

## Tecnologias
* [PHP](http://php.net/)
* [Zend Framework 1](https://framework.zend.com/manual/1.12/en/learning.quickstart.html) 
* [Composer](https://getcomposer.org/)
* [jQuery](https://jquery.com/)
* [Vuejs](https://vuejs.org/)
* [Materialize](http://materializecss.com/)
* [SqlServer](https://www.microsoft.com/en-us/sql-server/sql-server-2017)

## Documenta&ccedil;&atilde;o sobre a aplica&ccedil;&atilde;o
* [Esquema de desenvolvimento e banco](doc/Esquema_de_desenvolvimento_e_banco.md)
* [Guia de operação e desenvolvimento](doc/Guia_de_operacao-desenvolvimento.md)
* [Regras de versionamento](doc/Regras_versionamento.md)
* [Roteiro de publicação de releases](doc/Roteiro_de_publicacao_de_releases.md) - contém o git workflow
* [Teste e PHPUnit](doc/Teste_Manual.md)
* [SQLs](https://github.com/culturagovbr/salic-minc-sql) de apoio ao desenvolvimento do Salic


## Autores
Várias pessoas colaboraram com o desenvimento do projeto SALIC e decidimos centralizar em um único local todos os que participaram com o desenvolvimento do projeto.
  
Clique [aqui](doc/Autores.md) para visualizar.
