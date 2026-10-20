Pesquisa: APIs e sua utilização em sistemas



**Introdução**

Atualmente, muitos sistemas precisam trocar informações entre si para funcionar corretamente. Um exemplo disso acontece quando uma loja virtual consulta o estoque de produtos ou quando um aplicativo utiliza um serviço de pagamento. Para que essa comunicação aconteça, são utilizadas as APIs, que facilitam a integração entre diferentes aplicações.

1. **O que é uma API e qual é sua finalidade?**

API é a sigla para Application Programming Interface, que significa Interface de Programação de Aplicações. Ela funciona como uma ponte que permite que dois sistemas diferentes se comuniquem e compartilhem informações.
Sua finalidade é facilitar a integração entre programas, permitindo que uma aplicação utilize recursos de outra sem precisar conhecer todos os detalhes de seu funcionamento.
Por exemplo, uma loja virtual pode utilizar uma API de pagamento para processar compras sem precisar desenvolver seu próprio sistema bancário.

2. **Como funciona a comunicação entre sistemas por meio de APIs?**

A comunicação acontece quando um sistema envia uma solicitação para outro sistema por meio de uma API. O sistema que recebe a solicitação processa as informações e retorna uma resposta.
Esse processo geralmente acontece pela internet, utilizando o protocolo HTTP ou HTTPS. A resposta pode conter dados solicitados, uma confirmação de operação ou uma mensagem de erro.
Um exemplo seria um sistema de vendas que consulta a API do estoque para verificar se determinado produto está disponível.

3. **O que são APIs REST?**
As APIs REST são interfaces que seguem um conjunto de princípios de arquitetura chamado REST (Representational State Transfer). Elas são muito utilizadas no desenvolvimento de sistemas web porque permitem consultar, cadastrar, atualizar e excluir informações por meio de requisições HTTP.
Normalmente, essas APIs trabalham com recursos identificados por URLs e utilizam formatos como JSON para enviar e receber dados. Uma característica importante do REST é que cada requisição deve conter as informações necessárias para ser processada, sem depender de um estado de sessão mantido entre as requisições.

4. **O papel dos métodos HTTP: GET, POST, PUT, PATCH e DELETE**

Os métodos HTTP indicam qual ação o sistema deseja realizar em determinado recurso.
Esses métodos ajudam a organizar as operações e tornam a comunicação entre sistemas mais padronizada.

5. **O que são requisições e respostas HTTP?**

As requisições HTTP são mensagens enviadas por um cliente, como um navegador ou sistema, para um servidor. Elas indicam qual operação deve ser realizada e podem conter informações, cabeçalhos e dados.
As respostas HTTP são as mensagens devolvidas pelo servidor após o processamento da requisição. Elas podem conter os dados solicitados, uma confirmação ou uma mensagem de erro.
Por exemplo, quando um sistema solicita a lista de produtos, o servidor pode responder com o código 200, que indica sucesso. Se o produto não for encontrado, pode retornar o código 404.

6. **O que são endpoints?**

Endpoints são os endereços específicos disponibilizados por uma API para acessar determinados recursos ou executar operações.
Por exemplo:
https://exemplo.com/api/produtos — endereço para acessar os produtos.
https://exemplo.com/api/produtos/10 — endereço para acessar o produto de número 10.
O método HTTP utilizado também influencia a operação realizada no endpoint. Assim, o mesmo endereço pode permitir consultar um produto com GET ou atualizar suas informações com PUT, dependendo das regras da API.

7. **Como funciona a troca de dados no formato JSON?**

JSON é a sigla para JavaScript Object Notation. É um formato de texto utilizado para organizar e transmitir informações entre sistemas. Ele é bastante utilizado em APIs porque é simples de ler e pode ser interpretado por diferentes linguagens de programação.
Exemplo de dados de um produto em JSON:
{
  "id": 10,
  "nome": "Teclado",
  "preco": 150.00,
  "estoque": 20
}
Nesse exemplo, as informações do produto estão organizadas em pares de chave e valor. O sistema que recebe esses dados pode interpretar cada informação e utilizá-la em suas operações.

8. **Diferenças entre uma API e um sistema web tradicional**

Um sistema web tradicional geralmente apresenta páginas e interfaces para que as pessoas possam interagir diretamente com ele. Já uma API é voltada principalmente para a comunicação entre aplicações, disponibilizando dados e funcionalidades por meio de requisições.
Sistema web tradicional
API
Geralmente apresenta páginas e telas ao usuário.
Geralmente disponibiliza dados e funcionalidades.
Pode retornar HTML com textos, imagens e formulários.
Pode retornar JSON ou outros formatos de dados.
É utilizado diretamente pelo usuário.
É frequentemente utilizada por outros sistemas.
Pode reunir interface, regras de negócio e acesso aos dados.
Pode separar a comunicação da interface utilizada pelo usuário.

É importante lembrar que uma aplicação web também pode utilizar uma API internamente, e uma API pode fazer parte do mesmo sistema que apresenta as páginas.

9. **Como uma aplicação PHP pode consumir uma API?**

Uma aplicação PHP pode consumir uma API enviando requisições HTTP e recebendo as respostas do servidor. Para isso, pode utilizar recursos como a biblioteca cURL, que permite configurar requisições, enviar dados e obter respostas.
Depois de receber os dados em JSON, o PHP pode utilizar a função json_decode() para transformar o conteúdo em uma estrutura que possa ser manipulada pelo código.
Exemplo simples em PHP:
<?php
$url = "https://jsonplaceholder.typicode.com/posts/1";

ch=curlinit(url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

resposta=curlexec(ch);

if ($resposta === false) {
    echo "Erro ao acessar a API.";
} else {
    dados=jsondecode(resposta);

    if (json_last_error() === JSON_ERROR_NONE) {
        echo $dados->title;
    } else {
        echo "Erro ao interpretar os dados.";
    }
}

curl_close($ch);
?>
Nesse exemplo, o PHP consulta uma API de demonstração, recebe os dados de uma publicação e exibe o título. Para executar o código, é necessário ter o PHP com suporte ao cURL habilitado e acesso à internet.
Manual

10. **Como uma API pode disponibilizar informações para outros sistemas?**

Uma API pode disponibilizar informações por meio de endpoints que recebem requisições e retornam dados de acordo com as permissões e regras definidas pelo sistema.
Por exemplo, um sistema empresarial pode disponibilizar uma API para que outros programas consultem produtos, preços, clientes e pedidos. Quando um sistema autorizado solicita essas informações, a API consulta os dados necessários e retorna uma resposta, geralmente em JSON.
Dessa maneira, diferentes aplicações podem compartilhar informações sem precisar utilizar diretamente o banco de dados umas das outras. Isso facilita a integração e a manutenção dos sistemas.

11. **Importância da autenticação, autorização e segurança no consumo de APIs**

A segurança é importante porque as APIs podem permitir acesso a informações pessoais, financeiras e empresariais. Por isso, é necessário controlar quem pode acessar os recursos e quais operações podem ser realizadas.
Autenticação: verifica a identidade do usuário ou sistema que está tentando acessar a API.
Autorização: determina quais informações e operações esse usuário ou sistema pode acessar.
HTTPS: protege a comunicação durante a transmissão dos dados.
Tokens e chaves de API: podem ser utilizados para identificar e controlar o acesso dos clientes.
Validação dos dados: ajuda a evitar o processamento de informações inválidas ou maliciosas.
Por exemplo, em um sistema de estoque, um funcionário pode ter permissão para consultar produtos, enquanto somente um administrador pode cadastrar ou excluir registros. Além disso, as credenciais devem ser protegidas e as permissões verificadas pelo servidor em cada operação.

12. **Exemplos de utilização de APIs em sistemas empresariais**

As APIs são utilizadas em diversas áreas das empresas para integrar serviços e automatizar tarefas. Alguns exemplos são:
Comércio eletrônico: consulta de estoque, cadastro de produtos e atualização de pedidos.
Sistemas financeiros: integração com serviços de pagamento e consulta de transações.
Logística: consulta de fretes, rastreamento de entregas e atualização do status dos pedidos.
Recursos humanos: integração de sistemas de funcionários, folha de pagamento e controle de ponto.
Gestão empresarial: compartilhamento de informações entre sistemas de vendas, estoque, compras e financeiro.
Relatórios e análise de dados: obtenção de informações de diferentes sistemas para gerar relatórios.
Essas aplicações mostram como as APIs ajudam a automatizar processos, reduzir a repetição de tarefas e permitir que diferentes sistemas trabalhem em conjunto.
Conclusão
Com esta pesquisa, foi possível compreender que as APIs são importantes para a comunicação entre sistemas e para a integração de diferentes tecnologias. Elas permitem compartilhar informações, executar operações e automatizar processos empresariais. Também foi possível entender que métodos HTTP, endpoints e JSON fazem parte desse funcionamento, enquanto a autenticação, a autorização e outras medidas de segurança ajudam a proteger os dados. Portanto, as APIs são ferramentas fundamentais para o desenvolvimento de aplicações modernas.

**Referências**
As informações foram pesquisadas em documentação técnica e materiais de organizações reconhecidas para fazer esta pesquisa.

1. Mozilla Developer Network (MDN) — Métodos de requisição HTTP. Explica os métodos utilizados nas requisições. https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Methods
2. Mozilla Developer Network (MDN) — Visão geral do HTTP. Apresenta o funcionamento das requisições e respostas HTTP. https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/Overview
3. OWASP — REST Security Cheat Sheet. Apresenta conceitos de APIs REST e recomendações de segurança. https://cheatsheetseries.owasp.org/cheatsheets/REST_Security_Cheat_Sheet.html
4. OWASP — Authorization Cheat Sheet. Explica a importância da autorização e do controle de acesso. https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html
5. PHP — Manual oficial, exemplos básicos do cURL. Demonstra como enviar requisições HTTP e dados JSON utilizando PHP. https://www.php.net/manual/pt_BR/curl.examples-basic.php
6. OWASP — REST Assessment Cheat Sheet. Apresenta características das APIs REST e sua utilização na comunicação entre aplicações. https://cheatsheetseries.owasp.org/cheatsheets/REST_Assessment_Cheat_Sheet.html
