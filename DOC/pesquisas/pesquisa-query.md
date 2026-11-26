Pesquisa das query´s

DQL (Data Query Language)

SELECT- Recupera dados de uma ou mais tabelas
WITH- organiza consultas complexas ou recursivas.
VALUES- cria um conjunto de linhas de dados diretamente, sem precisar ler uma tabela.
TABLE- Atalho suportado por alguns bancos

DML (Data Manipulation Language)

INSERT- Adiciona novas linhas de dados em uma tabela
UPDATE- Modifica dados existentes em uma ou mais linhas de uma tabela.
DELETE- Remove linhas exitentes de uma tabela
MERGE- sincroniza tabelas
UPSERT- Insere uma linha ou atualiza caso já exista conflito de chave
REPLACE- Remove a linha antiga e insere uma nova caso haja conflito de chave
CALL- Executa uma Stored Procedure
DO- Executa um bloco de código anônimo ou avalia expressões sem retornar linhas de resultado.
HANDLER- Abre uma tabela para leitura direta linha a linha
LOAD DATA- Importa grandes volumes de dados de um arquivo de texto
COPY- Importa ou exporta dados em massa entre um arquivo do sistema e uma tabela

DDL (Data Definition Language)

CREATE- Cria novos objetos no banco de dados
ALTER- Modifica a estrutura de um objeto existente
DROP- Exclui permanentemente um objeto e seus dados do banco.
TRUNCATE- Remove rapidamente todas as linhas de uma tabela sem registrar exclusões individuais, mantendo a estrutura.
RENAME- Altera o nome de um objeto existente no banco de dados.
COMMENT- Adiciona ou modifica descrições de metadados associadas a objetos do banco.

DCL (Data Control Language)

GRANT- Concede privilégios e permissões de acesso a usuários ou papéis
REVOKE- Remove privilégios anteriormente concedidos a usuários ou papéis.
DENY- Bloqueia explicitamente uma permissão para um usuário, sobrepondo-se a permissões herdadas

TCL (Transaction Control Language)

START TRANSACTION- Inicia explicitamente um novo bloco de transação atômica.
BEGIN- Inicia um bloco de transação
COMMIT- Grava permanentemente no banco todas as alterações feitas na transação atual.
ROLLBACK- Desfaz todas as alterações feitas desde o início da transação ou até um SAVEPOINT.
SAVEPOINT- Cria um ponto de marcação intermediário dentro de uma transação para reversão parcial.
RELEASE SAVEPOINT- Remove um ponto de salvamento definido anteriormente sem desfazer as operações.
SET TRANSACTION- Define características da transação atual, como nível de isolamento ou modo somente leitura.
SET CONSTRAINTS- Define quando as restrições de integridade (como chaves estrangeiras) são verificadas na transação.
LOCK TABLE- Bloqueia explicitamente uma ou mais tabelas para controlar o acesso concorrente.
UNLOCK TABLES- Libera bloqueios de tabelas adquiridos anteriormente na sessão atual.

Sessão, Sistema e Diagnóstico

SET- Altera parâmetros de configuração da sessão atual, do sistema ou variáveis de ambiente.
RESET- Restaura um parâmetro de configuração alterado para seu valor padrão.
SHOW- Exibe valores atuais de variáveis de sistema, status do servidor ou estrutura de objetos.
DESCRIBE- Mostra a estrutura das colunas, tipos e chaves de uma tabela
EXPLAIN- Exibe o plano de execução de uma query sem necessariamente executá-la.
ANALYZE- Coleta e atualiza estatísticas sobre a distribuição dos dados nas tabelas para ajudar o otimizador de consultas.
VACUUM- Recupera espaço em disco ocupado por registros obsoletos e atualiza visibilidade de dados
OPTIMIZE- Reorganiza o armazenamento físico de tabelas e índices para reduzir fragmentação
CHECK- Verifica a integridade estrutural de tabelas e índices em busca de corrupção.
REPAIR- Tenta corrigir tabelas corrompidas
USE- Seleciona qual banco de dados será o padrão para os comandos subsequentes na sessão.
PREPARE- Compila e armazena uma instrução SQL parametrizada para execuções repetidas com maior desempenho.
EXECUTE- Executa uma instrução previamente compilada pelo comando PREPARE.
DEALLOCATE- Libera da memória uma instrução preparada anteriormente.
DECLARE- Define um cursor para iteração sequencial sobre resultados.
OPEN- Executa a consulta associada a um cursor e o posiciona antes da primeira linha.
FETCH- Recupera a próxima linha (ou bloco de linhas) de um cursor aberto.
CLOSE- Fecha um cursor aberto e libera os recursos de memória associados.
KILL- Encerra forçadamente uma conexão ou consulta em andamento no servidor.
FLUSH- Limpa ou recarrega caches internos, logs ou tabelas abertas do servidor
PURGE- Apaga arquivos de log binário antigos ou itens da lixeira do banco de dados.
BACKUP- Cria uma cópia de segurança do banco de dados ou de logs de transação
RESTORE- Restaura um banco de dados a partir de um arquivo de backup.
CHECKPOINT- Força a gravação imediata em disco de todas as páginas de dados modificadas na memória
REINDEX- Reconstrói índices corrompidos ou inchados do zero.
CLUSTER- Reordena fisicamente os dados de uma tabela no disco com base em um índice específico
DISCARD- Limpa o estado temporário da sessão atual, incluindo tabelas temporárias e planos preparados
LISTEN- Registra a sessão atual para escutar notificações assíncronas enviadas por um canal específico
NOTIFY- Envia uma notificação assíncrona com carga opcional para todas as sessões que escutam o canal
UNLISTEN- Cancela o registro de escuta de notificações de um canal.
REFRESH MATERIALIZED VIEW- Recalcula e atualiza os dados armazenados em uma view materializada.
SECURITY LABEL- Aplica rótulos de controle de acesso obrigatório (como SELinux) a objetos do banco.
IMPORT FOREIGN SCHEMA- Importa definições de tabelas de um servidor externo para acesso federado