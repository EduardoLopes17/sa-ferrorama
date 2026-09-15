Pesquisa sobre PDO
O que é PDO?

PDO significa PHP Data Objects. Ele é um recurso do PHP usado para fazer a comunicação entre uma aplicação e um banco de dados.

Com o PDO, podemos conectar o sistema ao banco e realizar operações como consultar, cadastrar, alterar e excluir informações.

Uma das principais características do PDO é que ele não funciona somente com o MySQL. Ele também pode ser utilizado com outros bancos de dados, como PostgreSQL e SQLite.

Para que o PDO é utilizado?

O PDO é utilizado para facilitar a comunicação entre o PHP e o banco de dados. Por meio dele, uma aplicação consegue enviar comandos SQL e receber informações armazenadas no banco.

Por exemplo, em um sistema de cadastro, o PDO pode ser usado para:

Cadastrar novos usuários;
Consultar usuários já cadastrados;
Alterar informações;
Excluir registros;
Fazer buscas no banco de dados.

Dessa forma, ele ajuda a conectar a parte do sistema feita em PHP com os dados que ficam armazenados no banco.

Como funciona uma conexão com PDO?

Para criar uma conexão com PDO, são informados alguns dados do banco, como servidor, nome do banco, usuário e senha.

Um exemplo simples de conexão com MySQL seria:

<?php

$servidor = "localhost";
$banco = "ferroramas";
$usuario = "root";
$senha = "";

try {
    $pdo = new PDO(
        "mysql:host=$servidor;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexão realizada com sucesso!";
} catch (PDOException $e) {
    echo "Erro ao conectar com o banco de dados.";
}
?>


Nesse exemplo, o try tenta realizar a conexão com o banco. Se alguma coisa der errado, o catch é responsável por tratar o erro.

O PDO::ERRMODE_EXCEPTION faz com que o PDO informe uma exceção quando ocorrer algum problema na conexão ou em uma operação realizada no banco.

Principais características do PDO

Algumas das principais características do PDO são:

Pode trabalhar com diferentes bancos de dados;
Possui suporte a Prepared Statements;
Permite tratamento de erros;
Trabalha com programação orientada a objetos;
Permite realizar transações;
Facilita a execução de comandos SQL;
Ajuda a tornar as consultas mais seguras.

Uma característica que chama bastante atenção é a possibilidade de utilizar o PDO com diferentes bancos de dados. Isso pode ser útil em projetos que podem mudar de banco futuramente.

Diferença entre PDO e MySQLi

O PDO e o MySQLi são duas formas de conectar uma aplicação PHP a um banco de dados.

A principal diferença é que o MySQLi foi criado especificamente para trabalhar com MySQL, enquanto o PDO pode trabalhar com vários tipos de banco de dados.

PDO	MySQLi
Trabalha com vários bancos de dados	Trabalha principalmente com MySQL
Suporta Prepared Statements	Suporta Prepared Statements
Orientado a objetos	Orientado a objetos e também procedural
Possui suporte a transações	Possui suporte a transações
Possui uma interface mais padronizada	É específico para MySQL

No nosso projeto, estamos utilizando o MySQLi, pois essa foi a tecnologia definida para esta etapa. O PDO foi estudado como uma alternativa para entender outra maneira de fazer a conexão com o banco.

Vantagens do PDO

O PDO possui algumas vantagens importantes.

A primeira é o suporte a diferentes bancos de dados. Se futuramente um sistema precisar deixar de utilizar MySQL e passar para outro banco compatível, o PDO pode facilitar essa mudança.

Outra vantagem é o uso de Prepared Statements, que tornam as consultas mais seguras.

Também existe uma forma organizada de tratar erros utilizando exceções, além do suporte a transações.

Desvantagens do PDO

Apesar de possuir várias vantagens, o PDO também tem alguns pontos que podem ser considerados desvantagens.

Para quem está começando no PHP, a utilização do PDO pode parecer um pouco mais complicada por trabalhar com programação orientada a objetos.

Além disso, é necessário ter o driver correto instalado para o banco de dados que será utilizado.

Em um projeto que utiliza somente MySQL, o MySQLi também pode ser uma opção adequada, principalmente quando a equipe já possui conhecimento sobre ele.

O que são Prepared Statements?

Prepared Statements, ou instruções preparadas, são uma forma mais segura de executar consultas SQL.

Em vez de colocar diretamente os valores recebidos pelo usuário dentro da consulta, utilizamos parâmetros.

Por exemplo:

$sql = "SELECT * FROM usuarios WHERE email = :email";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "email" => $email
]);


Nesse caso, o valor do e-mail é enviado separadamente da estrutura da consulta.

Isso é importante porque ajuda a evitar problemas de SQL Injection. Esse tipo de ataque pode acontecer quando uma aplicação permite que dados enviados por usuários sejam interpretados como parte de um comando SQL.

Por esse motivo, o uso de Prepared Statements é uma prática importante em sistemas que trabalham com banco de dados.

Por que Prepared Statements são importantes?

Os Prepared Statements ajudam a separar o comando SQL dos dados que serão utilizados nele.

Isso evita que um valor enviado pelo usuário seja interpretado como parte do comando SQL, aumentando a segurança da aplicação.

Além da segurança, eles também deixam o código mais organizado e podem facilitar o trabalho com consultas que precisam ser executadas várias vezes usando valores diferentes.

Quando o PDO pode ser uma boa escolha?

O PDO pode ser uma boa escolha em projetos que precisam trabalhar com diferentes bancos de dados ou que podem precisar trocar de banco no futuro.

Também pode ser interessante para projetos que utilizam bastante programação orientada a objetos, Prepared Statements e transações.

Por outro lado, se o sistema vai utilizar somente MySQL, o MySQLi também pode atender muito bem às necessidades do projeto.

PDO no projeto Ferroramas

No Ferroramas, a conexão com o banco de dados nesta etapa está sendo feita utilizando MySQLi, pois essa é a tecnologia solicitada na atividade.

Mesmo assim, pesquisar sobre PDO é importante para conhecer outra opção disponível no PHP.

Depois de comparar os dois, podemos perceber que ambos conseguem realizar operações com MySQL e possuem suporte a Prepared Statements. A principal diferença é que o PDO permite trabalhar com diferentes tipos de bancos, enquanto o MySQLi é voltado para o MySQL.

Por isso, dependendo do projeto e de suas necessidades, o PDO pode ser uma alternativa interessante.

Conclusão

O PDO é uma ferramenta do PHP que permite conectar aplicações a bancos de dados e realizar diversas operações com os dados armazenados.

Entre suas principais vantagens estão o suporte a diferentes bancos, o tratamento de erros e o uso de Prepared Statements, que ajudam na segurança das consultas.

Apesar de o nosso projeto utilizar MySQLi nesta etapa, conhecer o PDO é importante para entender que existem diferentes maneiras de trabalhar com bancos de dados no PHP.

A escolha entre PDO e MySQLi depende das necessidades de cada projeto. Para aplicações que utilizam apenas MySQL, os dois podem ser boas opções, enquanto o PDO pode ser mais interessante quando existe a possibilidade de trabalhar com diferentes bancos de dados.

Fontes consultadas
PHP.net — Documentação oficial do PHP sobre PDO: https://www.php.net/manual/pt_BR/book.pdo.php
PHP.net — Documentação sobre PDO::prepare: https://www.php.net/manual/pt_BR/pdo.prepare.php
PHP.net — Documentação sobre PDOStatement::execute: https://www.php.net/manual/pt_BR/pdostatement.execute.php
PHP.net — Documentação sobre MySQLi: https://www.php.net/manual/pt_BR/book.mysqli.php