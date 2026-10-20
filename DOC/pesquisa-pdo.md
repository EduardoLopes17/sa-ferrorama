## Pesquisa sobre PDO no PHP

## **1. O que é PDO?**

PDO significa **PHP Data Objects**. Ele é um recurso do PHP utilizado para fazer a comunicação entre uma aplicação e um banco de dados.
Com o PDO, podemos conectar o sistema ao banco e realizar operações como consultar, cadastrar, alterar e excluir informações.
Uma das principais características do PDO é que ele não funciona somente com MySQL. Ele também pode ser utilizado com outros bancos de dados, como PostgreSQL e SQLite, desde que o driver correspondente esteja disponível.

## **2. Para que o PDO é utilizado?**

O PDO é utilizado para facilitar a comunicação entre o PHP e o banco de dados. Por meio dele, uma aplicação consegue executar comandos SQL e acessar informações armazenadas.

Em um sistema de cadastro, o PDO pode ser usado para:

- **Cadastrar** novos usuários;
- **Consultar** usuários já cadastrados;
- **Alterar** informações;
- **Excluir** registros;
- **Realizar** buscas no banco de dados.

Dessa forma, ele conecta a parte do sistema desenvolvida em PHP aos dados armazenados no banco.

## **3. Como funciona uma conexão com PDO?**

Para criar uma conexão com PDO, são informados alguns dados do banco, como servidor, nome do banco, usuário e senha.

**Exemplo de conexão com MySQL:**

```php
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

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    echo "Conexão realizada com sucesso!";

} catch (PDOException $e) {
    echo "Erro ao conectar com o banco de dados.";
}
```

Nesse exemplo, o `try` tenta realizar a conexão com o banco. Se ocorrer algum problema, o `catch` é responsável por tratar a exceção.
A configuração `PDO::ATTR_ERRMODE` com `PDO::ERRMODE_EXCEPTION` permite que os erros sejam tratados por meio de exceções.

## **4. Principais características do PDO**

As principais características do PDO são:

- **Compatibilidade:** trabalha com diferentes bancos de dados;
- **Prepared Statements:** permite executar consultas utilizando parâmetros;
- **Tratamento de erros:** possibilita utilizar exceções;
- **Orientação a objetos:** utiliza uma interface baseada em objetos;
- **Transações:** permite agrupar operações no banco de dados;
- **Segurança:** ajuda a proteger consultas quando os parâmetros são utilizados corretamente.

Uma característica importante é a possibilidade de trabalhar com diferentes bancos de dados compatíveis, o que pode facilitar mudanças futuras no projeto.

## **5. Diferença entre PDO e MySQLi**

O PDO e o MySQLi são duas formas de conectar uma aplicação PHP a um banco de dados.
A principal diferença é que o **PDO suporta diferentes bancos de dados**, enquanto o **MySQLi é específico para MySQL**.

| Característica | PDO | MySQLi |
|---|---|---|
| Bancos de dados | Diferentes bancos compatíveis | MySQL |
| Prepared Statements | Sim | Sim |
| Programação orientada a objetos | Sim | Sim |
| Programação procedural | Não | Sim |
| Transações | Sim | Sim |

No projeto Ferroramas, estamos utilizando **MySQLi**, pois essa foi a tecnologia definida para esta etapa. O PDO foi estudado como uma alternativa para conhecer outra forma de realizar a conexão com o banco.

## **6. Vantagens do PDO**

O PDO possui algumas vantagens importantes:

- **Suporte a diferentes bancos:** facilita o trabalho com bancos de dados compatíveis;
- **Segurança:** oferece suporte a Prepared Statements;
- **Organização:** utiliza programação orientada a objetos;
- **Tratamento de erros:** permite identificar e tratar problemas por meio de exceções;
- **Transações:** permite confirmar ou desfazer operações agrupadas.

## **7. Desvantagens do PDO**

Apesar de possuir várias vantagens, o PDO também apresenta alguns pontos que precisam ser considerados:

- Para quem está começando no PHP, sua utilização pode parecer mais complicada por envolver programação orientada a objetos.
- É necessário ter o driver correto instalado para o banco de dados utilizado.
- Em projetos que utilizam somente MySQL, o MySQLi também pode ser uma opção adequada.

## **8. O que são Prepared Statements?**

**Prepared Statements**, ou instruções preparadas, são uma forma mais segura de executar consultas SQL utilizando parâmetros.
Em vez de colocar diretamente os valores recebidos do usuário dentro da consulta, utilizamos parâmetros para enviar esses valores separadamente.

**Exemplo:**

```php
<?php

$sql = "SELECT * FROM usuarios WHERE email = :email";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    "email" => $email
]);

?>
```

Nesse exemplo, o método `prepare()` prepara a consulta, enquanto `execute()` executa o comando utilizando o valor informado para o parâmetro `:email`. Essa abordagem ajuda a evitar ataques de **SQL Injection**, que podem ocorrer quando dados enviados pelo usuário interferem indevidamente nos comandos SQL.

## **9. Por que Prepared Statements são importantes?**

Os Prepared Statements ajudam a separar o comando SQL dos dados que serão utilizados na consulta.
Isso aumenta a segurança da aplicação, pois os valores são tratados como dados, em vez de serem interpretados como parte do comando SQL.
Além disso, eles deixam o código mais organizado e facilitam a execução de consultas com diferentes valores.

## **10. Quando o PDO pode ser uma boa escolha?**

O PDO pode ser uma boa escolha em projetos que precisam trabalhar com diferentes bancos de dados ou que podem precisar trocar de banco no futuro. Também pode ser interessante para aplicações que utilizam programação orientada a objetos, Prepared Statements e transações.
Por outro lado, se o sistema utilizar somente MySQL, o MySQLi também pode atender muito bem às necessidades do projeto.

## **11. PDO no projeto Ferroramas**

No projeto Ferroramas, a conexão com o banco de dados nesta etapa está sendo realizada utilizando **MySQLi**, conforme a tecnologia definida para a atividade. Mesmo assim, pesquisar sobre PDO é importante para conhecer outra opção disponível no PHP.
Após comparar as duas alternativas, podemos perceber que ambas permitem realizar operações no MySQL e utilizar Prepared Statements.
A principal diferença é que o **PDO permite trabalhar com diferentes bancos de dados**, enquanto o **MySQLi é voltado especificamente para MySQL**. Por isso, dependendo das necessidades do projeto, o PDO pode ser uma alternativa interessante.

## **12. Conclusão**

O PDO é um recurso do PHP que permite conectar aplicações a bancos de dados e realizar operações com as informações armazenadas.
Entre suas principais vantagens estão o suporte a diferentes bancos de dados, o tratamento de erros e o uso de Prepared Statements, que ajudam a aumentar a segurança das consultas. Apesar de o projeto Ferroramas utilizar MySQLi nesta etapa, conhecer o PDO é importante para entender que existem diferentes maneiras de trabalhar com bancos de dados no PHP. A escolha entre PDO e MySQLi depende das necessidades de cada projeto. Para aplicações que utilizam apenas MySQL, os dois podem ser boas opções, enquanto o PDO pode ser mais interessante quando existe a possibilidade de trabalhar com diferentes bancos de dados.

## **13. Fontes consultadas**

- **PHP.net — Documentação oficial do PDO:** https://www.php.net/manual/pt_BR/book.pdo.php
- **PHP.net — Documentação sobre PDO::prepare:** https://www.php.net/manual/pt_BR/pdo.prepare.php
- **PHP.net — Documentação sobre PDOStatement::execute:** https://www.php.net/manual/pt_BR/pdostatement.execute.php
- **PHP.net — Documentação oficial do MySQLi:** https://www.php.net/manual/pt_BR/book.mysqli.php
