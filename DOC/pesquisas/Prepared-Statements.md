Prepared Statements 

O banco de dados MySQL suporta instruções preparadas. Uma instrução preparada ou uma instrução parametrizada é usada para executar o mesmo comando repetidamente com alta eficiência e proteger contra injeções SQL.

Fluxo básico

A execução de uma instrução preparada consiste em dois estágios: preparação e execução. No estágio de preparação um modelo de instrução é enviado ao servidor de banco de dados. O servidor realiza uma verificação de sintaxe e inicializa os recursos internos para uso posterior.

O servidor MySQL suporta o uso de reservas de espaço posicionais anônimas com ponto de interrogação (?).

A preparação é seguida pela execução. Durante a execução o cliente vincula valores aos parâmetros e envia-os ao servidor. O servidor executa a instrução com os valores vinculados usando os recursos internos criados anteriormente.


Exemplo #1 Instrução preparada

<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli("example.com", "user", "password", "database");

/* Instrução não preparada */
$mysqli->query("DROP TABLE IF EXISTS test");
$mysqli->query("CREATE TABLE test(id INT, label TEXT)");

/* Instrução preparada, estágio 1: prepara */
$stmt = $mysqli->prepare("INSERT INTO test(id, label) VALUES (?, ?)");

/* Instrução preparada, estágio 2: vincula e executa */
$id = 1;
$label = 'PHP';
$stmt->bind_param("is", $id, $label); // "is" significa que $id está vinculada como um inteiro e $label como uma string

$stmt->execute();
Execução repetida

Uma instrução preparada pode ser executada repetidas vezes. Em cada execução o valor atual da variável vinculada é avaliado e enviado ao servidor. A instrução não é analisada novamente. O modelo de instrução não é mais transferido ao servidor.


Exemplo #2 INSERT preparado uma vez, executado múltiplas vezes

<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli("example.com", "user", "password", "database");

/* Instrução não preparada */
$mysqli->query("DROP TABLE IF EXISTS test");
$mysqli->query("CREATE TABLE test(id INT, label TEXT)");

/* Instrução preparada, estágio 1: prepara */
$stmt = $mysqli->prepare("INSERT INTO test(id, label) VALUES (?, ?)");

/* Instrução preparada, estágio 2: vincula e executa */
$stmt->bind_param("is", $id, $label); // "is" significa que $id está vinculada como um inteiro e $label como uma string

$data = [
    1 => 'PHP',
    2 => 'Java',
    3 => 'C++'
];
foreach ($data as $id => $label) {
    $stmt->execute();
}

$result = $mysqli->query('SELECT id, label FROM test');
var_dump($result->fetch_all(MYSQLI_ASSOC));
O exemplo acima produzirá:

array(3) {
  [0]=>
  array(2) {
    ["id"]=>
    string(1) "1"
    ["label"]=>
    string(3) "PHP"
  }
  [1]=>
  array(2) {
    ["id"]=>
    string(1) "2"
    ["label"]=>
    string(4) "Java"
  }
  [2]=>
  array(2) {
    ["id"]=>
    string(1) "3"
    ["label"]=>
    string(3) "C++"
  }
}
Cada instrução preparada ocupa recursos do servidor. Instruções devem ser fechadas explicitamente logo após o uso. Se não for fechada explicitamente, a instrução será fechada quando o manipulador da instrução for liberado pelo PHP.

Usar uma instrução preparada não é sempre a maneira mais eficiente de executar um comando. Uma instrução preparada executada apenas uma vez causa mais idas e voltas entre cliente e servidor do que uma não preparada. Este é o motivo pelo qual o SELECT não é executado como uma instrução preparada no exemplo acima.

Além disso, considere o uso da sintaxe SQL multi-INSERT do MySQL para inserção de dados. Para o exemplo, multi-INSERT requer menos idas e voltas entre o servidor e o cliente do que a instrução preparada mostrada acima.


Exemplo #3 Menos idas e voltas usando o multi-INSERT do SQL

<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$mysqli = new mysqli("example.com", "user", "password", "database");

$mysqli->query("DROP TABLE IF EXISTS test");
$mysqli->query("CREATE TABLE test(id INT)");

$values = [1, 2, 3, 4];

$stmt = $mysqli->prepare("INSERT INTO test(id) VALUES (?), (?), (?), (?)");
$stmt->bind_param('iiii', ...$values);
$stmt->execute();
Tipos de dados dos valores do conjunto de resultados

O Protocolo Cliente-Servidor do MySQL define um protocolo de transferência de dados diferente entre instruções preparadas e não preparadas. Instruções preparadas usam o chamado protocolo binário. O servidor MySQL envia os dados do conjunto de resultado como eles realmente são, em formato binário. Os resultados não são serializados em strings antes do envio. Bibliotecas cliente recebem os dados binários e tentam converter os valores para os tipos de dados apropriados do PHP. Por exemplo, resultados de uma coluna INT do SQL serão fornecidos como variáveis do tipo inteiro do PHP.
