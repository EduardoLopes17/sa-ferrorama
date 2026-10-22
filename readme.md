<div align="center">

Ferrovia União HELG
Sistema de Gerenciamento e Monitoramento Ferroviário


<p align="center">
  <img class="icon" src="./assets/img/icone trem.png" alt="Ícone Ferrorama"> 
</p>
  

</div>

**Sobre o projeto**
A Ferrovia HELG é um projeto de desenvolvimento de sistemas voltado ao gerenciamento e monitoramento ferroviário. A proposta é reunir informações operacionais de uma ferrovia em uma interface web, facilitando o acompanhamento dos trens, o controle de cadastros e a consulta de dados.

O sistema também trabalha com conceitos de IoT, envolvendo sensores físicos e simulações para acompanhar o funcionamento da ferrovia.
Objetivos
- Desenvolver uma interface para gerenciamento ferroviário.
- Organizar os cadastros de usuários, trens e sensores.
- Possibilitar o acompanhamento das operações dos trens.
- Explorar a visualização de trajetos em um mapa interativo.
- Integrar simulações e dados de sensores físicos ao sistema, conforme a evolução do projeto.
️ Funcionalidades
Estas são as funcionalidades do projeto. O andamento será atualizado conforme as entregas.
Área	Funcionalidade prevista ou em desenvolvimento	Situação
Acesso	Login e logout de usuários	Em revisão
Usuários	Cadastro, consulta, alteração e exclusão de usuários	Em revisão
Permissões	Controle de perfis de acesso	Em revisão
Trens	Cadastro, consulta, alteração e exclusão de trens	Em revisão
Trens	Definição e alteração do usuário responsável pelo trem	Em revisão
Sensores	Cadastro e gerenciamento de sensores	Em revisão
Monitoramento	Acompanhamento dos trens e suas informações	Em desenvolvimento
Rotas	Representação de trajetos em mapa interativo	Planejado
Simulação	Execução de rotas com tempo simulado	Planejado
IoT	Integração com sensores físicos	Em desenvolvimento
Relatórios	Consulta e visualização de informações operacionais	Em revisão


**Tecnologias utilizadas**
Tecnologia	Utilização no projeto
HTML5	Estrutura das páginas
CSS3	Estilização da interface
JavaScript	Interatividade e funcionalidades no navegador
PHP	Lógica do sistema no servidor
MySQL	Armazenamento e consulta de dados
XAMPP	Ambiente de desenvolvimento local
Git e GitHub	Versionamento e colaboração
IoT / sensores	Integração física proposta para monitoramento


️**Estrutura do projeto**
A estrutura de pastas será adicionada após a conferência da versão atual do repositório.

Repositório: EduardoLopes17/sa-ferrorama
Como executar o projeto
Este projeto utiliza um ambiente local com PHP, MySQL e XAMPP. 
1. Instale o XAMPP e o Git.
2. Clone o repositório dentro da pasta htdocs do XAMPP:
   git clone https://github.com/EduardoLopes17/sa-ferrorama.git
3. Inicie os serviços Apache e MySQL no painel do XAMPP.
4. Acesse o phpMyAdmin e importe o arquivo SQL correspondente ao banco de dados do projeto, disponível no projeto.
5. Confira as credenciais e a porta do MySQL no arquivo de conexão utilizado pelo projeto.
6. Abra o projeto pelo navegador usando o endereço local correspondente à pasta clonada, por exemplo:
   http://localhost/sa-ferrorama/
Monitoramento e IoT
A proposta da Ferrovia HELG contempla duas formas de trabalho:
- Simulação digital: representação do deslocamento dos trens e de seus trajetos no sistema.
- Integração física: utilização de sensores e componentes de IoT para coletar informações relacionadas à operação ferroviária.
As duas partes fazem parte do desenvolvimento do projeto.

**Requisitos Funcionais**

RF1 - O sistema deve permitir login com nome de usuário e senha, bloqueando o acesso em caso de credenciais inválidas. 

RF2 - O sistema deve disponibilizar ao Adm a tela de cadastro com os campos: nome completo, CPF, telefone, e-mail, endereço, cidade, estado, senha e confirmação de senha. 

RF3 - O sistema deve permitir navegação entre as telas de login e cadastro por meio de links contextuais. 

RF4 - O sistema deve exibir modal de confirmação ao acionar "Sair", encerrando a sessão e redirecionando ao login somente após confirmação do usuário. 

RF5 - O sistema deve exibir painel com os indicadores: total de trens ativos, sensores online, taxa de sucesso operacional e tempo médio de viagem.  

RF6 - O sistema deve listar os trens em operação com ID, rota, status, velocidade atual e próxima parada estimada. 

RF7 - O sistema deve exibir banner de alertas ativos no topo do dashboard, com contagem e descrição sumária das ocorrências. 

RF8 - O sistema deve permitir cadastrar novos sensores informando ID, localização e tipo do sensor. 

RF9 - O sistema deve exibir na mesma tela a lista de sensores já cadastrados com ID, localização e tipo. 

RF10 - O sistema deve listar todos os sensores com as colunas ID, localização, tipo e status, diferenciando visualmente Ativo (verde) de Inativo (vermelho).  

RF11 - O sistema deve permitir alterar o status de um sensor entre Ativo e Inativo diretamente na tela de gerenciamento.  

RF12 - O sistema deve exibir painel detalhado do trem selecionado com: ID, rota, velocidade atual, velocidade média, status e próxima parada com tempo estimado.  

RF13 - O sistema deve indicar explicitamente quando o trem está sem atrasos ou com atraso registrado, com destaque visual diferenciado para cada situação.  

RF14 - O sistema deve registrar e exibir alertas com data/hora, tipo (sensor, trem ou sistema), descrição e nível de severidade. 

RF15 - O sistema deve classificar alertas em três níveis de severidade — Alta, Média e Baixa — com diferenciação visual por cor em cada linha da listagem.  

RF16 - O sistema deve exibir os alertas em ordem cronológica decrescente, com os eventos mais recentes no topo da listagem.  

RF17 - O sistema deve permitir gerar relatórios configuráveis por tipo, período e formato de saída. 

RF18 - O sistema deve listar os relatórios gerados recentemente com nome e data de geração.  

RF19 - O sistema deve suportar ao menos o formato PDF como saída disponível para geração de relatórios. 

RF20 - O sistema deve disponibilizar barra de navegação persistente em todas as telas internas, com acesso a todos os módulos e à opção de sair, além do logotipo da empresa no cabeçalho. 

**Requisitos Não Funcionais**

RN1 - A interface deve ser intuitiva, com nomenclatura em português brasileiro e feedback visual claro para ações do usuário. 

RN2 - As senhas devem ser armazenadas de forma criptografada. A sessão deve ser encerrada de forma segura ao clicar em "Sair". 

RN3 - O sistema deve estar disponível 24/7, dado que o monitoramento ferroviário é contínuo e crítico. 

RN4 - O dashboard deve atualizar os dados dos trens e sensores em tempo próximo ao real, com latência máxima tolerável de 30 segundos. 

RN5 - A interface deve se adaptar a diferentes resoluções de tela sem perda de funcionalidade. 

RN6 - O código deve ser modular por tela/módulo para facilitar evolução e manutenção independente de cada seção. 

RN7 - A identidade da marca deve ser mantida em todas as telas. 

RN8 - Todos os alertas gerados devem ser armazenados com registro de data/hora para fins de auditoria. 

**Metodologia de desenvolvimento**
A equipe utiliza Scrum e Kanban para organizar o desenvolvimento.

O Scrum é utilizado para planejar as entregas, enquanto o Kanban ajuda a acompanhar as tarefas pendentes, em andamento e concluídas.

O código é mantido no GitHub.
Desenvolvedores
1. Eduardo Lopes
2. Guilherme Floriano
3. Hugo Gramme
4. Luis Gustavo
Repositório
GitHub: Ferrovia HELG — SA Ferrorama
<div align="center">

Ferrovia HELG
Sistema de Gerenciamento e Monitoramento Ferroviário

Projeto em desenvolvimento
</div>
