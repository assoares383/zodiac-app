# Zodiac App

Aplicação web acadêmica para identificação de signos zodiacais a partir da
data de nascimento. O projeto foi desenvolvido em PHP puro, com XML/SimpleXML,
Nginx, PHP-FPM, Docker Compose e Bootstrap 5.

## Objetivo acadêmico

O projeto demonstra:

- processamento de formulário HTML com método `POST`;
- validação de dados no servidor;
- manipulação de arquivo XML com `simplexml_load_file()`;
- comparação de datas e tratamento de ano bissexto;
- regra de intervalo que atravessa a mudança de ano;
- separação entre apresentação, serviço e fonte de dados;
- execução reproduzível com containers Docker.

Não são utilizados frameworks PHP, Apache, banco de dados ou Dockerfile
adicional.

## Tecnologias

- PHP `8.2-fpm`;
- Nginx `alpine`;
- Docker Compose;
- SimpleXML;
- HTML5, CSS3 e JavaScript;
- Bootstrap 5 via CDN;
- testes PHP executados diretamente pela CLI.

## Pré-requisitos

- Docker;
- Docker Compose;
- navegador web.

Não é necessário instalar PHP, Composer ou banco de dados no computador host.

## Execução

Na raiz do projeto, execute:

```bash
docker compose up -d
docker compose ps
```

Acesse:

```text
http://localhost:8080
```

Para interromper os containers:

```bash
docker compose down
```

### Verificar o PHP e o SimpleXML

```bash
docker compose exec php php -v
docker compose exec php php -m
```

A saída do segundo comando deve conter `SimpleXML`.

## Testes automatizados

A suíte não utiliza PHPUnit nem outro framework. Execute:

```bash
docker compose exec -T php php tests/run_tests.php
```

A suíte verifica:

- identificação dos 12 signos;
- primeiro e último dia dos intervalos;
- transições entre signos;
- Capricórnio em dezembro e janeiro;
- ano bissexto e 29 de fevereiro;
- datas inválidas, vazias e futuras;
- XML válido;
- XML inexistente, malformado e estruturalmente inválido.

Resultado esperado:

```text
Resumo: 49 testes executados, 49 aprovados, 0 falhos.
```

O script retorna `exit(0)` quando todos os testes passam e `exit(1)` quando
qualquer teste falha.

## Estrutura do projeto

```text
zodiac-app/
├── public/
│   ├── index.php
│   ├── show_zodiac_sign.php
│   ├── layouts/header.php
│   └── assets/
│       ├── css/style.css
│       └── js/script.js
├── src/
│   ├── data/signos.xml
│   ├── SignoService.php
│   └── SignoXmlRepository.php
├── tests/
│   └── run_tests.php
├── docker/
│   └── nginx/default.conf
├── docs/
│   ├── brainstorm.md
│   ├── plan.md
│   └── specification.md
├── docker-compose.yml
└── README.md
```

### Responsabilidades

- `public/index.php`: formulário inicial.
- `public/show_zodiac_sign.php`: recebe o POST e exibe resultado ou erro.
- `public/layouts/header.php`: layout HTML compartilhado.
- `public/assets/`: estilos e comportamento complementar da interface.
- `src/data/signos.xml`: fonte oficial dos doze signos.
- `src/SignoXmlRepository.php`: carrega e valida o XML.
- `src/SignoService.php`: valida datas e identifica o signo.
- `tests/run_tests.php`: suíte automatizada PHP CLI.
- `docker/nginx/default.conf`: configuração do Nginx e FastCGI.

O Nginx utiliza `public/` como raiz pública. O diretório `src/` não deve ser
acessível diretamente pelo navegador.

## Dados dos signos

O arquivo [signos.xml](src/data/signos.xml) contém exatamente 12 registros,
com as tags:

- `dataInicio`;
- `dataFim`;
- `signoNome`;
- `descricao`.

A tabela canônica possui limites inclusivos:

| Signo | Início | Término |
|---|---:|---:|
| Capricórnio | 22/12 | 20/01 |
| Aquário | 21/01 | 19/02 |
| Peixes | 20/02 | 20/03 |
| Áries | 21/03 | 20/04 |
| Touro | 21/04 | 20/05 |
| Gêmeos | 21/05 | 20/06 |
| Câncer | 21/06 | 22/07 |
| Leão | 23/07 | 22/08 |
| Virgem | 23/08 | 22/09 |
| Libra | 23/09 | 22/10 |
| Escorpião | 23/10 | 21/11 |
| Sagitário | 22/11 | 21/12 |

Após validar a data completa no formato `YYYY-MM-DD`, o serviço converte mês e
dia para:

```text
MMDD = mês * 100 + dia
```

Capricórnio é tratado com:

```text
MMDD >= 1222 || MMDD <= 120
```

O ano original continua sendo necessário para rejeitar datas futuras e validar
29 de fevereiro em anos bissextos.

## Respostas conceituais

### Qual é a função do XML?

O XML é a fonte estruturada dos dados dos signos. Ele separa nomes, limites e
descrições da lógica PHP e permite demonstrar leitura de dados estruturados com
SimpleXML sem utilizar banco de dados.

### Por que manipular as datas?

O formulário fornece ano, mês e dia, enquanto os limites dos signos possuem
apenas dia e mês. A conversão para `MMDD` permite comparações numéricas
corretas. O ano é validado antes da conversão para tratar datas futuras e anos
bissextos.

## Evidências acadêmicas

| Evidência | Arquivo |
|---|---|
| Formulário inicial | `public/index.php` |
| Resultado da consulta | `public/show_zodiac_sign.php` |
| Cabeçalho | `public/layouts/header.php` |
| Dados XML | `src/data/signos.xml` |
| Serviço de negócio | `src/SignoService.php` |
| Repositório XML | `src/SignoXmlRepository.php` |
| Testes | `tests/run_tests.php` |

As páginas exibem mensagens amigáveis, escapam dados dinâmicos com
`htmlspecialchars()` e não expõem caminhos internos ou stack traces.

## Validação final realizada

- `docker compose config`: configuração válida;
- containers `php` e `nginx`: iniciados com imagens oficiais;
- extensão `SimpleXML`: disponível no container PHP;
- sintaxe dos módulos PHP: aprovada;
- suíte CLI: 49 testes aprovados;
- formulário `POST`: funcional;
- resultado com data formatada em `DD/MM/YYYY`: funcional;
- data futura: rejeitada;
- acesso direto a `src/`: bloqueado pelo Nginx;
- fluxo HTTP local: funcional em `http://localhost:8080`.
