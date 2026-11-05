# Brainstorming — Zodiac App

Quero desenvolver uma aplicação acadêmica chamada **Zodiac App**, como parte de uma atividade prática da disciplina de desenvolvimento web da Anhanguera.

Utilize a skill **brainstorming** do Superpowers para conduzir a descoberta dos requisitos e a definição do design da aplicação.

## 1. Contexto acadêmico

A atividade prática tem como objetivo ensinar os estudantes a desenvolver uma aplicação web dinâmica utilizando PHP puro.

Os principais conhecimentos que precisam ser demonstrados são:

- Manipulação de arquivos XML.
- Utilização da biblioteca SimpleXML do PHP.
- Criação e processamento de formulários HTML.
- Envio de dados utilizando o método HTTP POST.
- Implementação de lógica condicional em PHP.
- Comparação e manipulação de datas.
- Estilização de páginas utilizando Bootstrap 5.
- Organização básica de uma aplicação web.

O resultado esperado é um sistema capaz de identificar o signo zodiacal de uma pessoa a partir da sua data de nascimento, consultando as informações armazenadas em um arquivo XML.

## 2. Tecnologias obrigatórias

A aplicação deverá utilizar:

- PHP puro, sem frameworks.
- HTML5.
- CSS3.
- JavaScript.
- Bootstrap 5.
- XML.
- SimpleXML.
- Nginx.
- PHP-FPM.
- Docker Compose.

### Restrições técnicas

Não utilizar:

- XAMPP.
- Apache.
- Laravel.
- Symfony.
- Outros frameworks PHP.
- Banco de dados.
- MySQL ou PostgreSQL.

O ambiente de desenvolvimento deverá ser executado exclusivamente por meio de containers Docker, utilizando Docker Compose para orquestrar os serviços.

Preferencialmente, utilizar imagens oficiais, evitando criar um Dockerfile desnecessariamente. Caso alguma configuração ou extensão PHP seja necessária e não esteja disponível na imagem escolhida, apresentar a justificativa antes de propor sua inclusão.

## 3. Requisitos funcionais

A aplicação deverá atender aos seguintes requisitos:

### RF01 — Página inicial

Disponibilizar uma página inicial com:

- Nome da aplicação Zodiac App.
- Título e breve apresentação.
- Formulário para informar a data de nascimento.
- Campo de entrada do tipo data.
- Botão para consultar o signo.

### RF02 — Envio do formulário

O formulário deverá:

- Utilizar o método POST.
- Enviar o campo `data_nascimento`.
- Encaminhar os dados para a página responsável pelo processamento.
- Impedir o processamento de dados ausentes ou inválidos.

### RF03 — Consulta ao XML

A aplicação deverá consultar um arquivo chamado `signos.xml`.

O arquivo deverá armazenar os doze signos zodiacais, contendo:

- Data de início.
- Data de término.
- Nome do signo.
- Descrição do signo.

Estrutura conceitual esperada:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<signos>
    <signo>
        <dataInicio>21/03</dataInicio>
        <dataFim>20/04</dataFim>
        <signoNome>Áries</signoNome>
        <descricao>Descrição do signo.</descricao>
    </signo>
</signos>
```

O arquivo deverá conter os doze signos, com informações em português.

A leitura deverá ser realizada utilizando `simplexml_load_file()`.

### RF04 — Identificação do signo

O sistema deverá:

1. Receber a data de nascimento.
2. Validar a data recebida.
3. Carregar o arquivo XML.
4. Percorrer os signos cadastrados.
5. Comparar a data de nascimento com os intervalos definidos no XML.
6. Identificar o signo correspondente.
7. Apresentar o resultado ao usuário.

A lógica deverá considerar corretamente os intervalos que atravessam a mudança de ano, como Capricórnio, cujo período começa em dezembro e termina em janeiro.

### RF05 — Resultado da consulta

Após o processamento, apresentar:

- Nome do signo identificado.
- Data de nascimento informada.
- Descrição do signo.
- Opção para retornar à página inicial e realizar uma nova consulta.

### RF06 — Tratamento de erros

A aplicação deverá tratar situações como:

- Data de nascimento não informada.
- Data inválida.
- Data futura.
- Arquivo XML inexistente.
- Arquivo XML inválido ou malformado.
- Nenhum signo correspondente encontrado.

As mensagens deverão ser apresentadas de maneira amigável, sem expor detalhes internos da aplicação.

## 4. Requisitos não funcionais

A aplicação deverá apresentar:

- Separação de responsabilidades entre apresentação, processamento e regras de negócio.
- Código organizado e legível.
- Validação de dados no servidor utilizando PHP.
- Validação complementar no navegador quando apropriado.
- Proteção contra saída HTML não escapada.
- Tratamento adequado de erros.
- Responsividade utilizando Bootstrap 5.
- Compatibilidade com os navegadores modernos.
- Configuração reproduzível por Docker Compose.
- Documentação de instalação e execução.
- Testes das principais regras de negócio.

A aplicação deverá permanecer simples e compatível com o nível de complexidade da atividade acadêmica, evitando abstrações ou dependências desnecessárias.

## 5. Estrutura inicial do projeto

A atividade acadêmica apresenta a seguinte estrutura:

```text
zodiac-app/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── imgs/
│   └── js/
├── layouts/
│   └── header.php
├── index.php
├── show_zodiac_sign.php
└── signos.xml
```

Quero avaliar se essa estrutura pode ser aprimorada para uma organização mais profissional, sem deixar de atender às exigências da atividade.

Considere, por exemplo, a possibilidade de separar:

- Arquivos públicos da aplicação.
- Componentes de apresentação.
- Regras de negócio.
- Serviços responsáveis pela leitura do XML.
- Arquivos de configuração do Nginx.
- Configuração do Docker Compose.
- Testes automatizados.
- Documentação.

Não aumente a complexidade sem necessidade e preserve os nomes dos arquivos exigidos pela atividade, quando tecnicamente adequado.

## 6. Ambiente Docker

A aplicação deverá utilizar dois serviços principais:

**Nginx**
- Servidor web responsável por receber as requisições HTTP.
- Servir os arquivos estáticos.
- Encaminhar as requisições PHP para o PHP-FPM.

**PHP-FPM**
- Executar os scripts PHP.
- Disponibilizar a extensão SimpleXML.
- Processar os formulários e a lógica de identificação dos signos.

O ambiente deverá ser gerenciado pelo Docker Compose.

Quero avaliar a configuração mais simples e adequada para desenvolvimento local, considerando volumes, portas, comunicação entre containers e permissões de arquivos.

Não quero criar configurações ou serviços que não sejam necessários para esta aplicação.

## 7. Testes e qualidade

Quero que o design considere testes para:

- Identificação dos doze signos.
- Datas exatamente nos limites dos intervalos.
- Datas de transição entre dois signos.
- Períodos que atravessam a mudança de ano.
- Datas inválidas.
- Datas futuras.
- Dados ausentes no formulário.
- Problemas na leitura do XML.

Avalie se vale a pena utilizar PHPUnit ou se existe uma alternativa mais simples e adequada ao escopo acadêmico.

## 8. Documentação e entrega acadêmica

A implementação deverá permitir produzir as evidências solicitadas pela Anhanguera.

O relatório final deverá contemplar:

- Capa.
- Folha de rosto com os dados da disciplina e do aluno.
- Métodos utilizados.
- Captura de tela do formulário inicial.
- Captura de tela do resultado da consulta.
- Evidência do arquivo `signos.xml`.
- Evidência do arquivo `index.php`.
- Evidência do arquivo `header.php`.
- Evidência do arquivo `show_zodiac_sign.php`.
- Explicação dos procedimentos realizados.

Também deverão ser respondidas as questões acadêmicas:

1. Qual a função do arquivo XML nesta aplicação?
2. Por que foi necessário manipular as datas antes de compará-las?

O projeto deverá possuir um README.md com instruções para instalação, execução, testes e estrutura da aplicação.

## 9. Pontos que precisam ser investigados

Antes de propor a arquitetura, investigue possíveis inconsistências e decisões técnicas.

Em especial:

1. A atividade acadêmica apresenta apenas `dataInicio` e `dataFim` no formato DD/MM. Qual é a melhor maneira de comparar esses intervalos com uma data completa de nascimento?
2. Como tratar corretamente o intervalo de Capricórnio, que atravessa a mudança de ano?
3. Como garantir que todos os dias do calendário estejam contemplados sem sobreposição ou lacunas?
4. Quais datas e critérios devem ser utilizados para os limites dos signos? Considere que as datas apresentadas em fontes populares podem variar.
5. Como preservar a utilização de XML e SimpleXML como parte central da atividade, evitando substituir essa exigência por outra tecnologia?
6. Como organizar a aplicação sem criar uma arquitetura excessivamente complexa para um projeto acadêmico?
7. Como configurar Nginx e PHP-FPM utilizando Docker Compose e imagens oficiais?
8. Quais testes são realmente necessários para comprovar o funcionamento?
9. Como manter os arquivos e evidências exigidos pela Anhanguera, mesmo com uma estrutura interna aprimorada?

Não assuma que todas as informações do enunciado estão tecnicamente corretas. Aponte inconsistências e apresente alternativas.

## 10. Condução do brainstorming

Siga o processo da skill brainstorming do Superpowers.

Quero que você:

1. Investigue os requisitos apresentados.
2. Identifique ambiguidades, inconsistências e riscos técnicos.
3. Faça perguntas quando houver decisões importantes que dependam da minha escolha.
4. Apresente alternativas com vantagens e desvantagens.
5. Proponha uma arquitetura compatível com o escopo acadêmico.
6. Apresente o fluxo de funcionamento da aplicação.
7. Defina os principais componentes e suas responsabilidades.
8. Descreva a estratégia de validação e testes.
9. Apresente o design completo para minha avaliação.

Faça uma pergunta de cada vez quando precisar de esclarecimentos importantes.

## 11. Restrições desta etapa

**Nesta etapa, não implemente código e não crie arquivos.**

Não crie:

- Arquivos PHP.
- Arquivos XML.
- Arquivos HTML.
- Arquivos CSS ou JavaScript.
- Docker Compose.
- Configurações Nginx.
- Testes.
- README.

Não inicie a implementação nem o planejamento detalhado de tarefas.

O objetivo desta etapa é exclusivamente investigar os requisitos e validar o design da Zodiac App.

**Aguarde minha aprovação explícita do design antes de avançar para a skill writing-plans do Superpowers.**