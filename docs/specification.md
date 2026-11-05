# Zodiac App — Especificação Técnica e Arquitetura

## 1. Objetivo

O Zodiac App é uma aplicação web acadêmica desenvolvida em PHP puro para
identificar o signo zodiacal de uma pessoa a partir de sua data de nascimento.

O projeto deve demonstrar:

- processamento de formulários HTML;
- envio de dados por HTTP POST;
- validação de dados no servidor;
- manipulação de arquivo XML;
- utilização da extensão SimpleXML;
- comparação e manipulação de datas;
- separação básica de responsabilidades;
- uso de HTML5, CSS3, JavaScript e Bootstrap 5;
- execução reproduzível com Docker Compose.

Não serão utilizados banco de dados, frameworks PHP, Apache ou Dockerfiles
adicionais.

## 2. Decisões técnicas

| Item | Decisão |
|---|---|
| Linguagem | PHP puro |
| Versão do PHP | 8.2 |
| Servidor web | Nginx |
| Processamento PHP | PHP-FPM |
| Imagens Docker | `php:8.2-fpm` e `nginx:alpine` |
| Orquestração | Docker Compose |
| Persistência | Arquivo XML |
| Fonte dos signos | `src/data/signos.xml` |
| Leitura XML | `simplexml_load_file()` |
| Comparação de datas | Conversão numérica para `MMDD` |
| Testes | Scripts PHP executados via CLI |
| Framework PHP | Nenhum |
| Interface | HTML5, CSS3, JavaScript e Bootstrap 5 |

## 3. Arquitetura de pastas

```text
zodiac-app/
├── public/
│   ├── index.php
│   ├── show_zodiac_sign.php
│   ├── layouts/
│   │   └── header.php
│   └── assets/
│       ├── css/
│       │   └── style.css
│       ├── js/
│       │   └── script.js
│       └── imgs/
├── src/
│   ├── data/
│   │   └── signos.xml
│   ├── SignoService.php
│   └── SignoXmlRepository.php
├── tests/
│   ├── SignoServiceTest.php
│   └── SignoXmlRepositoryTest.php
├── nginx/
│   └── default.conf
├── docs/
│   ├── brainstorm.md
│   └── specification.md
├── docker-compose.yml
└── README.md
```

### Responsabilidades

- `public/index.php`: apresenta a página inicial e o formulário.
- `public/show_zodiac_sign.php`: recebe o POST e apresenta resultado ou erro.
- `src/data/signos.xml`: fonte oficial dos doze signos.
- `public/layouts/header.php`: elementos HTML comuns da apresentação.
- `src/SignoXmlRepository.php`: carrega e valida o XML.
- `src/SignoService.php`: valida datas e identifica o signo.
- `tests/`: testes independentes executáveis pela CLI.
- `nginx/default.conf`: configuração do servidor web e do FastCGI.
- `docker-compose.yml`: definição dos serviços e volumes.
- `README.md`: instalação, execução, testes e estrutura.

Somente `public/` deve ser a raiz pública do Nginx. O diretório `src/` não deve
ser acessível diretamente pelo navegador.

## 4. Requisitos funcionais

### RF01 — Página inicial

A aplicação deve exibir o nome Zodiac App, uma breve apresentação, um campo
`data_nascimento` do tipo `date` e um botão de consulta.

### RF02 — Envio do formulário

O formulário deve:

- utilizar o método `POST`;
- enviar o campo `data_nascimento`;
- apontar para `show_zodiac_sign.php`;
- possuir validação HTML complementar.

### RF03 — Consulta ao XML

O arquivo XML deve conter exatamente doze elementos `<signo>`, cada um com:

- `dataInicio`;
- `dataFim`;
- `signoNome`;
- `descricao`.

A leitura deve ser realizada por `simplexml_load_file()`.

### RF04 — Identificação

O sistema deve validar a data, carregar o XML, percorrer os signos, comparar os
intervalos e exibir o signo correspondente. O intervalo de Capricórnio deve
funcionar corretamente na mudança de ano.

### RF05 — Resultado

O resultado deve apresentar:

- nome do signo;
- data informada;
- descrição;
- link para nova consulta.

### RF06 — Erros

Devem ser tratados dados ausentes, datas inválidas ou futuras, XML inexistente,
XML malformado, estrutura XML inválida e ausência de signo correspondente.

## 5. Fluxo de dados

```text
Usuário
  |
  | informa a data
  v
public/index.php
  |
  | POST: data_nascimento
  v
public/show_zodiac_sign.php
  |
  | valida presença e formato
  v
SignoService
  |
  | valida calendário, ano bissexto e data futura
  | converte a data para MMDD
  v
SignoXmlRepository
  |
  | executa simplexml_load_file()
  | transforma os nós XML em dados utilizáveis
  v
SignoService
  |
  | compara os intervalos
  | aplica a regra de Capricórnio
  v
show_zodiac_sign.php
  |
  | escapa a saída HTML
  | apresenta resultado ou mensagem amigável
  v
Usuário
```

A regra de negócio não deve depender de elementos HTML, permitindo que seja
testada diretamente por scripts PHP CLI.

## 6. Formato e validação do XML

Exemplo mínimo:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<signos>
    <signo>
        <dataInicio>22/12</dataInicio>
        <dataFim>20/01</dataFim>
        <signoNome>Capricórnio</signoNome>
        <descricao>Descrição de Capricórnio.</descricao>
    </signo>
</signos>
```

O repositório XML deve verificar:

- existência e leitura do arquivo;
- retorno de `simplexml_load_file()`;
- XML bem formado;
- presença dos campos obrigatórios;
- quantidade de doze signos;
- datas de início e término válidas.

Falhas técnicas devem ser registradas de forma apropriada, mas nunca exibidas
com caminhos internos, stack traces ou mensagens do libxml ao usuário.

## 7. Tabela canônica dos signos

Esta tabela é a única referência para o XML, os testes e o relatório acadêmico.
Os limites são inclusivos.

| Ordem | Signo | Início | Término |
|---:|---|---:|---:|
| 1 | Capricórnio | 22/12 | 20/01 |
| 2 | Aquário | 21/01 | 19/02 |
| 3 | Peixes | 20/02 | 20/03 |
| 4 | Áries | 21/03 | 20/04 |
| 5 | Touro | 21/04 | 20/05 |
| 6 | Gêmeos | 21/05 | 20/06 |
| 7 | Câncer | 21/06 | 22/07 |
| 8 | Leão | 23/07 | 22/08 |
| 9 | Virgem | 23/08 | 22/09 |
| 10 | Libra | 23/09 | 22/10 |
| 11 | Escorpião | 23/10 | 21/11 |
| 12 | Sagitário | 22/11 | 21/12 |

Datas de fontes populares podem variar. Para evitar inconsistências, a tabela
acima deve ser aplicada de modo uniforme em todos os artefatos do projeto.

## 8. Regra de comparação de datas

Após validar a data completa, o sistema deve convertê-la para:

```text
MMDD = mês * 100 + dia
```

Exemplos:

```text
21/03 -> 321
05/04 -> 405
20/01 -> 120
22/12 -> 1222
```

Para intervalos normais:

```text
início <= MMDD && MMDD <= término
```

Para Capricórnio, cujo intervalo atravessa o ano:

```text
MMDD >= 1222 || MMDD <= 120
```

Consequentemente, 22/12, 31/12, 01/01 e 20/01 pertencem a Capricórnio,
enquanto 21/01 pertence a Aquário.

### Validação do calendário

O ano real é utilizado antes da conversão para `MMDD`. A aplicação deve:

1. verificar se o campo foi enviado;
2. confirmar o formato `YYYY-MM-DD`;
3. validar a data com `DateTime::createFromFormat()`;
4. confirmar que a data normalizada corresponde à entrada;
5. aceitar 29/02 somente em ano bissexto;
6. rejeitar datas futuras;
7. converter mês e dia para `MMDD`.

Assim, `29/02/2024` é válido e `29/02/2025` é inválido.

## 9. Diagrama mental dos containers Docker

```text
                         Navegador
                             |
                             | HTTP :8080
                             v
                 +-------------------------+
                 | nginx                   |
                 | imagem: nginx:alpine    |
                 | raiz: /var/www/html/    |
                 |       public            |
                 +------------+------------+
                              |
                              | FastCGI :9000
                              | serviço: php
                              v
                 +-------------------------+
                 | php                     |
                 | imagem: php:8.2-fpm     |
                 | executa scripts PHP     |
                 | utiliza SimpleXML       |
                 +-------------------------+

       Volumes compartilhados:
       ./public -> /var/www/html/public
       ./src    -> /var/www/html/src
```

O acesso local será:

```text
http://localhost:8080
```

O Nginx serve arquivos estáticos e encaminha PHP para `php:9000`. O PHP-FPM
executa os scripts, carrega o XML e processa a regra de negócio. Os serviços
serão definidos no Docker Compose usando somente as imagens oficiais
`php:8.2-fpm` e `nginx:alpine`, sem Dockerfile adicional.

A disponibilidade da extensão SimpleXML deve ser confirmada no container com:

```bash
docker compose exec php php -m
```

## 10. Matriz de tratamento de erros

| Situação | Detecção | Mensagem ao usuário | Tratamento técnico |
|---|---|---|---|
| Campo ausente | `isset()`/`array_key_exists()` | Informe sua data de nascimento. | Encerrar processamento |
| Campo vazio | `trim()` vazio | Informe sua data de nascimento. | Encerrar processamento |
| Formato inválido | Falha no formato esperado | Informe uma data válida. | Encerrar processamento |
| Data inexistente | Validação `DateTime` falha | A data informada não existe. | Encerrar processamento |
| 29/02 inválido | Ano não bissexto | A data informada não existe. | Encerrar processamento |
| Data futura | Data maior que hoje | A data de nascimento não pode ser futura. | Encerrar processamento |
| XML inexistente | `is_file()`/leitura falha | Não foi possível carregar os dados dos signos. | Registrar erro técnico |
| XML sem permissão | Falha de leitura | Não foi possível carregar os dados dos signos. | Registrar erro técnico |
| XML malformado | SimpleXML retorna `false` | Os dados dos signos estão indisponíveis. | Registrar erros do libxml |
| Campos XML ausentes | Validação estrutural | Os dados dos signos estão inválidos. | Registrar inconsistência |
| Quantidade diferente de 12 | Contagem dos nós | Os dados dos signos estão inválidos. | Registrar inconsistência |
| Nenhum signo | Nenhum intervalo coincide | Não foi possível identificar o signo. | Registrar inconsistência |
| Método HTTP incorreto | Método diferente de POST | Acesso inválido. | Redirecionar ou encerrar |
| Falha inesperada | Exceção controlada | Ocorreu um erro ao processar a consulta. | Registrar sem expor detalhes |

Todas as saídas inseridas no HTML devem ser escapadas com:

```php
htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')
```

## 11. Testes CLI

Os testes serão scripts PHP sem PHPUnit:

```bash
php tests/SignoServiceTest.php
php tests/SignoXmlRepositoryTest.php
```

Devem cobrir:

- pelo menos um caso de cada um dos doze signos;
- primeiro e último dia de cada intervalo;
- dias imediatamente próximos aos limites;
- Capricórnio em 22/12, 31/12, 01/01 e 20/01;
- transição de 20/01 para 21/01;
- datas inválidas e inexistentes;
- datas futuras;
- campo vazio ou ausente;
- 29/02 em ano bissexto e não bissexto;
- XML válido;
- XML inexistente;
- XML malformado;
- XML sem campos obrigatórios;
- XML com quantidade incorreta de signos.

Um teste com falha deve terminar com código de saída diferente de zero.

## 12. Conformidade acadêmica

| Evidência solicitada | Local |
|---|---|
| Formulário inicial | `public/index.php` |
| Processamento | `public/show_zodiac_sign.php` |
| Cabeçalho | `public/layouts/header.php` |
| Arquivo XML | `src/data/signos.xml` |
| Regras de negócio | `src/SignoService.php` |
| Leitura do XML | `src/SignoXmlRepository.php` |

O README deve explicar que a estrutura original foi aprimorada com `public/` e
`src/`, sem remover os arquivos conceitualmente exigidos pela atividade.

## 13. Respostas conceituais da Anhanguera

### 13.1 Qual é a função do arquivo XML?

O XML é a fonte estruturada de dados dos signos zodiacais. Ele armazena, para
cada signo, as datas inicial e final, o nome e a descrição. A aplicação lê
esses dados em tempo de execução por meio da extensão SimpleXML.

Essa escolha separa os dados da lógica PHP, permite manutenção simples e
atende ao objetivo acadêmico de demonstrar a leitura e manipulação de dados
estruturados sem utilizar banco de dados.

### 13.2 Por que foi necessário manipular as datas?

O formulário fornece uma data completa, com ano, mês e dia, enquanto o XML
armazena apenas dia e mês. Além disso, comparar strings como `21/03` e `05/04`
não representa corretamente a ordem cronológica.

Por isso, depois de validar a data completa, mês e dia são convertidos para o
valor numérico `MMDD`. A comparação torna-se objetiva e permite tratar
explicitamente Capricórnio, cujo intervalo cruza a mudança do ano.

O ano continua sendo necessário para validar datas futuras e situações como
29 de fevereiro.

## 14. Riscos e controles

| Risco | Controle |
|---|---|
| Fontes populares divergem nas datas | Usar a tabela canônica em XML, testes e relatório |
| XML editado incorretamente | Validar estrutura antes de processar |
| SimpleXML indisponível | Confirmar a extensão no container PHP |
| Exposição de código interno | Configurar `public/` como raiz do Nginx |
| Dependência de JavaScript | Manter toda validação essencial no PHP |
| Mensagens internas expostas | Usar mensagens amigáveis e registro técnico separado |
| Lacunas ou sobreposição de intervalos | Testar todos os limites e a virada do ano |
| Diferença de fuso horário | Documentar e padronizar o horário usado para data atual |

## 15. Critérios de aceite

A implementação será considerada conforme quando:

- iniciar com Docker Compose;
- utilizar Nginx e PHP-FPM;
- servir somente `public/` como raiz web;
- processar o formulário via POST;
- ler `signos.xml` com `simplexml_load_file()`;
- identificar corretamente os doze signos;
- tratar Capricórnio na virada do ano;
- validar datas reais, futuras e anos bissextos;
- exibir nome, data e descrição no resultado;
- tratar erros sem expor informações internas;
- executar e aprovar os testes PHP CLI;
- manter os arquivos exigidos para as evidências acadêmicas;
- documentar instalação, execução, testes e estrutura no README.
