# Zodiac App — Plano de Implementação

Este plano organiza a implementação em fases incrementais, respeitando a ordem
arquitetural definida em `docs/specification.md`:

1. Docker e ambiente;
2. camada de dados XML;
3. lógica de negócio em PHP puro;
4. testes automatizados via CLI;
5. páginas HTML/Bootstrap e documentação acadêmica.

Nenhuma tarefa deste documento deve ser interpretada como implementação já
realizada. Cada fase deve ser concluída e validada antes do início da fase
seguinte.

## Fase 1 — Docker e ambiente

### 1.1 Definir o Compose

- [x] Criar `docker-compose.yml` com os serviços `nginx` e `php`.
  - Usar exclusivamente as imagens oficiais `nginx:alpine` e `php:8.2-fpm`.
  - Publicar a porta local `8080` para a porta `80` do Nginx.
  - Configurar a dependência do serviço Nginx em relação ao PHP-FPM.
  - Compartilhar os diretórios `public/` e `src/` entre os containers.
  - Não adicionar banco de dados, Apache, Composer ou serviços desnecessários.
- **Validação:** executar `docker compose config` e confirmar que a configuração
  é válida, que os dois serviços foram reconhecidos e que não há Dockerfile
  adicional sendo utilizado.

### 1.2 Configurar o Nginx

- [x] Criar `docker/nginx/default.conf`.
  - Definir `/var/www/html/public` como raiz pública.
  - Configurar o arquivo inicial da aplicação.
  - Encaminhar arquivos `.php` para `php:9000` por FastCGI.
  - Configurar corretamente `SCRIPT_FILENAME`.
  - Impedir que `src/` seja servido diretamente pelo navegador.
- **Validação:** iniciar os containers com `docker compose up -d`, verificar
  `docker compose ps` e confirmar que o Nginx iniciou sem erro de configuração.

### 1.3 Confirmar o ambiente PHP-FPM

- [x] Verificar a versão do PHP e a disponibilidade do PHP-FPM no container.
- [x] Confirmar que a extensão SimpleXML está disponível na imagem
  `php:8.2-fpm`.
- [x] Confirmar que o PHP consegue acessar os volumes montados de `public/` e
  `src/`.
- **Arquivos alterados:** nenhum arquivo de aplicação além de
  `docker-compose.yml` e `docker/nginx/default.conf`.
- **Validação:**
  - executar `docker compose exec php php -v`;
  - executar `docker compose exec php php -m`;
  - confirmar a presença de `SimpleXML`;
  - verificar que `docker compose logs nginx` e `docker compose logs php` não
    apresentam falhas de inicialização.

### 1.4 Validar o primeiro acesso HTTP

- [x] Criar temporariamente apenas o mínimo necessário para responder uma
  requisição HTTP, sem iniciar a implementação funcional da aplicação.
- [x] Confirmar o encaminhamento Nginx → PHP-FPM.
- [x] Remover qualquer arquivo temporário após a validação, caso ele não faça
  parte da estrutura final.
- **Arquivos envolvidos:** `public/index.php`, se necessário, e os arquivos da
  infraestrutura.
- **Validação:** acessar `http://localhost:8080` e confirmar uma resposta HTTP
  do PHP através do Nginx. Interromper e remover os containers ao terminar:
  `docker compose down`.

## Fase 2 — Camada de dados XML

### 2.1 Criar o arquivo canônico de signos

- [x] Criar `src/data/signos.xml`.
  - Declarar XML em UTF-8.
  - Criar a raiz `<signos>`.
  - Inserir exatamente os doze signos.
  - Usar os limites inclusivos definidos na especificação:
    `22/12–20/01`, `21/01–19/02`, `20/02–20/03`, `21/03–20/04`,
    `21/04–20/05`, `21/05–20/06`, `21/06–22/07`, `23/07–22/08`,
    `23/08–22/09`, `23/09–22/10`, `23/10–21/11` e `22/11–21/12`.
  - Preencher `dataInicio`, `dataFim`, `signoNome` e `descricao` em todos os
    nós.
- **Validação:** executar uma verificação XML no container PHP e confirmar que o
  arquivo é bem formado, possui doze nós `<signo>` e contém todos os campos
  obrigatórios.

### 2.2 Definir o contrato do repositório XML

- [X] Planejar a interface interna de `src/SignoXmlRepository.php`.
  - Receber o caminho do XML sem fixar caminhos absolutos na regra de negócio.
  - Carregar o arquivo usando obrigatoriamente `simplexml_load_file()`.
  - Converter os nós para uma estrutura simples e previsível.
  - Diferenciar falha de arquivo, XML malformado e estrutura inválida.
  - Evitar expor mensagens internas do libxml na camada web.
- **Validação:** revisar o contrato antes de implementar a camada de serviço e
  confirmar que ela pode ser usada tanto pela aplicação quanto pelos testes CLI.

### 2.3 Implementar o repositório XML

- [X] Criar `src/SignoXmlRepository.php`.
  - Verificar se o arquivo existe e pode ser lido.
  - Ativar o tratamento interno de erros do libxml durante a leitura.
  - Tratar o retorno `false` de `simplexml_load_file()`.
  - Validar quantidade, nomes e campos obrigatórios.
  - Validar os formatos `DD/MM` dos limites.
  - Retornar dados sem acoplar o XML à apresentação HTML.
- **Validação:** executar o PHP com `php -l src/SignoXmlRepository.php` e validar
  manualmente o carregamento de `src/data/signos.xml` dentro do container.

### 2.4 Preparar cenários de falha do XML

- [x] Definir arquivos XML temporários de teste fora do caminho público, ou
  criá-los e removê-los durante a execução dos testes.
- [x] Cobrir XML inexistente, XML malformado, campos ausentes e quantidade
  diferente de doze signos.
- **Arquivos envolvidos:** `src/SignoXmlRepository.php` e futuros arquivos em
  `tests/`.
- **Validação:** cada cenário deve produzir uma falha controlada, sem warning ou
  stack trace destinado ao usuário.

## Fase 3 — Lógica de negócio em PHP puro

### 3.1 Definir o contrato do serviço

- [x] Planejar `src/SignoService.php`.
  - Receber uma data no formato `YYYY-MM-DD`.
  - Validar a data completa antes de convertê-la.
  - Rejeitar campos vazios, formatos inválidos e datas futuras.
  - Aceitar 29/02 somente em anos bissextos.
  - Converter mês e dia para `MMDD`.
  - Consultar os signos por meio de `SignoXmlRepository`.
  - Retornar dados do signo identificado ou erro controlado.
- **Validação:** confirmar que o serviço não depende de `$_POST`, HTML, sessão ou
  funções específicas do navegador.

### 3.2 Implementar validação da data

- [x] Criar `src/SignoService.php`.
  - Usar `DateTime::createFromFormat()` ou mecanismo equivalente do PHP.
  - Confirmar que a data normalizada corresponde exatamente à entrada.
  - Aplicar a data atual de forma consistente para validar datas futuras.
  - Converter `mês * 100 + dia` para o valor `MMDD`.
- **Validação:** executar `php -l src/SignoService.php` e verificar casos válidos,
  inválidos, futuros e de ano bissexto em uma execução CLI isolada.

### 3.3 Implementar comparação dos intervalos

- [x] Implementar a comparação inclusiva para intervalos normais.
- [x] Implementar a regra especial de Capricórnio:
  `MMDD >= 1222 || MMDD <= 120`.
- [x] Garantir que o serviço utilize os limites fornecidos pelo XML, em vez de
  duplicar a tabela dentro da lógica.
- [x] Retornar o nome, os limites e a descrição do signo encontrado.
- **Validação:** verificar manualmente os limites de todos os intervalos e os
  casos `20/01 → Capricórnio` e `21/01 → Aquário`.

### 3.4 Validar a integração entre serviço e repositório

- [x] Integrar `SignoService` com `SignoXmlRepository`.
- [x] Garantir que falhas do repositório sejam convertidas em erros controlados
  pela camada de apresentação.
- [x] Garantir que nenhum detalhe interno seja incluído em mensagens públicas.
- **Validação:** executar uma consulta válida e consultas com XML indisponível,
  XML inválido e nenhum intervalo correspondente.

## Fase 4 — Testes automatizados via CLI

### 4.1 Criar infraestrutura mínima de testes

- [x] Criar `tests/run_tests.php`.
- [x] Implementar helpers PHP puros para asserções, mensagens de falha e código
  de saída.
- [x] Carregar as classes de `src/` sem Composer e sem PHPUnit.
- **Validação:** executar os dois scripts diretamente com `php` e confirmar que
  uma asserção deliberadamente inválida resulta em código de saída diferente de
  zero; remover a asserção temporária depois.

### 4.2 Testar os doze signos e os limites

- [x] Adicionar em `tests/run_tests.php` pelo menos um caso de cada
  signo.
- [x] Testar o primeiro e o último dia de cada intervalo.
- [x] Testar dias imediatamente anteriores e posteriores aos limites relevantes.
- [x] Confirmar que não existem lacunas ou sobreposições entre intervalos.
- **Validação:** executar `php tests/run_tests.php` e exigir que todos os
  casos terminem com sucesso.

### 4.3 Testar Capricórnio, bissexto e datas inválidas

- [x] Testar 22/12, 31/12, 01/01 e 20/01 como Capricórnio.
- [x] Testar 21/01 como Aquário.
- [x] Testar 29/02 em ano bissexto.
- [x] Testar 29/02 em ano não bissexto.
- [x] Testar data futura, data inexistente, formato inválido, campo vazio e
  entrada ausente.
- **Validação:** executar os testes via CLI e confirmar que cada caso produz o
  resultado ou erro esperado.

### 4.4 Testar a camada XML

- [x] Adicionar em `tests/run_tests.php` testes para o XML canônico.
- [x] Testar arquivo inexistente.
- [x] Testar XML malformado.
- [x] Testar ausência de campos obrigatórios.
- [x] Testar quantidade diferente de doze signos.
- [x] Limpar arquivos temporários ao fim de cada teste, inclusive em cenários
  de falha.
- **Validação:** executar `php tests/run_tests.php` e confirmar
  que os erros são detectados sem exibição de detalhes internos.

### 4.5 Consolidar o comando de testes

- [x] Definir no `README.md` os comandos oficiais para executar todos os testes.
- [x] Se necessário, criar um script simples de execução, sem introduzir
  framework ou dependência adicional.
- **Validação:** executar a suíte completa em uma única sequência e confirmar
  código de saída zero quando todos os testes passarem.

## Fase 5 — Páginas HTML, Bootstrap e documentação acadêmica

### 5.1 Criar o layout compartilhado

- [x] Criar `public/layouts/header.php`.
  - Definir HTML5, idioma português e metadados responsivos.
  - Incluir Bootstrap 5 conforme a estratégia escolhida para o projeto.
  - Incluir `public/assets/css/style.css`.
  - Manter o componente reutilizável e sem lógica de negócio.
- **Validação:** executar `php -l public/layouts/header.php` e verificar que a
  página renderiza sem HTML estrutural quebrado.

### 5.2 Criar a página inicial

- [x] Criar `public/index.php`.
  - Exibir o nome Zodiac App e sua apresentação.
  - Criar formulário com `method="POST"`.
  - Usar `name="data_nascimento"` e `type="date"`.
  - Apontar o formulário para `show_zodiac_sign.php`.
  - Adicionar validação HTML complementar e layout responsivo Bootstrap 5.
- [x] Criar `public/assets/css/style.css` apenas para ajustes visuais próprios.
- [x] Criar `public/assets/js/script.js` somente se houver comportamento de
  interface necessário, sem transferir a validação essencial para JavaScript.
- **Validação:** acessar `http://localhost:8080`, visualizar o formulário em
  desktop e viewport estreita e confirmar que o HTML contém POST e o nome de
  campo exigidos.

### 5.3 Criar o processamento e a página de resultado

- [x] Criar `public/show_zodiac_sign.php`.
  - Aceitar somente requisições POST para o fluxo normal.
  - Encaminhar `data_nascimento` ao `SignoService`.
  - Exibir nome, data e descrição do signo.
  - Exibir link para retornar à página inicial.
  - Mapear erros técnicos para mensagens amigáveis.
  - Escapar toda saída dinâmica com `htmlspecialchars()`.
- **Validação:** testar pelo navegador ou por uma requisição HTTP as consultas
  de um signo normal, Capricórnio, data inválida, data futura e campo ausente.
  Confirmar que nenhuma mensagem revela caminhos, stack traces ou detalhes do
  XML.

### 5.4 Verificar o fluxo completo no Docker

- [x] Iniciar o ambiente com `docker compose up -d`.
- [x] Confirmar que Nginx serve somente `public/`.
- [x] Confirmar que arquivos de `src/` não são baixados diretamente.
- [x] Confirmar que o POST chega ao PHP-FPM e que o XML é lido dentro do
  container.
- [x] Confirmar o funcionamento de todos os limites da tabela canônica por
  requisições HTTP.
- **Validação:** executar verificações HTTP contra
  `http://localhost:8080`, revisar `docker compose logs` e finalizar com
  `docker compose down` após os testes.

### 5.5 Escrever o README acadêmico

- [x] Criar `README.md`.
  - Descrever o objetivo e o contexto acadêmico.
  - Documentar pré-requisitos, Docker e Docker Compose.
  - Documentar `docker compose up -d` e o endereço local.
  - Documentar a verificação do SimpleXML.
  - Documentar os comandos dos testes CLI.
  - Explicar a estrutura `public/`, `src/`, `tests/`, `nginx/` e `docs/`.
  - Explicar a tabela canônica e a comparação `MMDD`.
  - Explicar o tratamento especial de Capricórnio e anos bissextos.
  - Responder às perguntas acadêmicas sobre o papel do XML e a manipulação de
    datas.
  - Indicar onde encontrar as evidências solicitadas: formulário, resultado,
    `signos.xml`, `index.php`, `header.php` e `show_zodiac_sign.php`.
  - Incluir limitações conhecidas, especialmente variações de datas em fontes
    populares.
- **Validação:** seguir o README em uma execução limpa, executar a aplicação e
  os testes sem depender de conhecimento implícito não documentado.

### 5.6 Revisão final de conformidade

- [x] Comparar a implementação final com todos os requisitos de
  `docs/specification.md`.
- [x] Confirmar ausência de frameworks PHP, Apache, banco de dados e Dockerfile
  adicional.
- [x] Confirmar que o XML continua sendo a fonte dos dados e que
  `simplexml_load_file()` é utilizado.
- [x] Confirmar que as mensagens de erro são amigáveis e que as saídas
  dinâmicas estão escapadas.
- [x] Confirmar que a documentação acadêmica está coerente com os arquivos
  efetivamente existentes.
- **Validação:** executar a suíte CLI, subir o Docker Compose, testar o fluxo
  web e revisar os arquivos entregues antes de considerar o projeto concluído.

## Critério de conclusão do plano

O plano estará concluído quando todas as tarefas estiverem marcadas como
realizadas, os testes CLI retornarem código zero, o ambiente Docker iniciar
com as duas imagens oficiais e o fluxo web completo atender à especificação e
às evidências acadêmicas exigidas.
