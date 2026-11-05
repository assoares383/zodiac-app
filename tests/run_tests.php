<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/SignoXmlRepository.php';
require_once __DIR__ . '/../src/SignoService.php';

$total = 0;
$aprovados = 0;

/**
 * Executa um caso de teste e imprime seu resultado.
 */
$executar = static function (string $nome, Closure $teste) use (&$total, &$aprovados): void {
    $total++;

    try {
        $teste();
        $aprovados++;
        echo "[PASS] {$nome}" . PHP_EOL;
    } catch (Throwable $erro) {
        echo "[FAIL] {$nome}: {$erro->getMessage()}" . PHP_EOL;
    }
};

$afirmar = static function (bool $condicao, string $mensagem): void {
    if (!$condicao) {
        throw new RuntimeException($mensagem);
    }
};

$afirmarExcecao = static function (
    Closure $operacao,
    string $classeEsperada,
    string $mensagem,
) use ($afirmar): void {
    try {
        $operacao();
    } catch (Throwable $erro) {
        $afirmar(
            $erro instanceof $classeEsperada,
            "{$mensagem}: classe recebida " . $erro::class,
        );

        return;
    }

    throw new RuntimeException("{$mensagem}: nenhuma exceção foi lançada.");
};

$dataAtualDosTestes = new DateTimeImmutable(
    '2026-11-05',
    new DateTimeZone('America/Sao_Paulo'),
);
$caminhoXml = __DIR__ . '/../src/data/signos.xml';
$repositorio = new SignoXmlRepository($caminhoXml);
$servico = new SignoService($repositorio, $dataAtualDosTestes);

$signosEsperados = [
    ['2020-01-05', 'Capricórnio'],
    ['2020-02-10', 'Aquário'],
    ['2020-03-10', 'Peixes'],
    ['2020-04-15', 'Áries'],
    ['2020-05-10', 'Touro'],
    ['2020-06-10', 'Gêmeos'],
    ['2020-07-10', 'Câncer'],
    ['2020-08-10', 'Leão'],
    ['2020-09-10', 'Virgem'],
    ['2020-10-10', 'Libra'],
    ['2020-11-10', 'Escorpião'],
    ['2020-12-10', 'Sagitário'],
];

foreach ($signosEsperados as [$data, $nomeEsperado]) {
    $executar(
        "identifica {$nomeEsperado}",
        static function () use ($servico, $data, $nomeEsperado, $afirmar): void {
            $signo = $servico->identificar($data);
            $afirmar($signo->nome === $nomeEsperado, "Esperado {$nomeEsperado}, recebido {$signo->nome}.");
            $afirmar($signo->descricao !== '', 'A descrição do signo não pode ser vazia.');
        },
    );
}

$limites = [
    ['2020-12-22', 'Capricórnio'],
    ['2020-01-20', 'Capricórnio'],
    ['2020-01-21', 'Aquário'],
    ['2020-02-19', 'Aquário'],
    ['2020-02-20', 'Peixes'],
    ['2020-03-20', 'Peixes'],
    ['2020-03-21', 'Áries'],
    ['2020-04-20', 'Áries'],
    ['2020-04-21', 'Touro'],
    ['2020-05-20', 'Touro'],
    ['2020-05-21', 'Gêmeos'],
    ['2020-06-20', 'Gêmeos'],
    ['2020-06-21', 'Câncer'],
    ['2020-07-22', 'Câncer'],
    ['2020-07-23', 'Leão'],
    ['2020-08-22', 'Leão'],
    ['2020-08-23', 'Virgem'],
    ['2020-09-22', 'Virgem'],
    ['2020-09-23', 'Libra'],
    ['2020-10-22', 'Libra'],
    ['2020-10-23', 'Escorpião'],
    ['2020-11-21', 'Escorpião'],
    ['2020-11-22', 'Sagitário'],
    ['2020-12-21', 'Sagitário'],
];

foreach ($limites as [$data, $nomeEsperado]) {
    $executar(
        "valida limite {$data}",
        static function () use ($servico, $data, $nomeEsperado, $afirmar): void {
            $signo = $servico->identificar($data);
            $afirmar($signo->nome === $nomeEsperado, "Esperado {$nomeEsperado}, recebido {$signo->nome}.");
        },
    );
}

$casosEspeciais = [
    ['2000-12-31', 'Capricórnio', 'Capricórnio na virada do ano'],
    ['2000-01-01', 'Capricórnio', 'Capricórnio no primeiro dia do ano'],
    ['2024-02-29', 'Peixes', '29 de fevereiro em ano bissexto'],
];

foreach ($casosEspeciais as [$data, $nomeEsperado, $descricao]) {
    $executar(
        $descricao,
        static function () use ($servico, $data, $nomeEsperado, $afirmar): void {
            $signo = $servico->identificar($data);
            $afirmar($signo->nome === $nomeEsperado, "Esperado {$nomeEsperado}, recebido {$signo->nome}.");
        },
    );
}

$entradasInvalidas = [
    ['', 'data vazia'],
    ['2024-02-30', 'data inexistente'],
    ['29/02/2024', 'formato inválido'],
    ['2025-02-29', '29 de fevereiro em ano não bissexto'],
    ['2026-11-06', 'data futura'],
];

foreach ($entradasInvalidas as [$data, $descricao]) {
    $executar(
        "rejeita {$descricao}",
        static function () use ($servico, $data, $afirmarExcecao): void {
            $afirmarExcecao(
                static fn (): SignoData => $servico->identificar($data),
                InvalidArgumentException::class,
                'A entrada inválida deveria ser rejeitada',
            );
        },
    );
}

$executar(
    'carrega os doze signos do XML',
    static function () use ($repositorio, $afirmar): void {
        $signos = $repositorio->obterTodos();
        $afirmar(count($signos) === 12, 'O XML deve conter doze signos.');
        $afirmar($signos[0]->nome === 'Capricórnio', 'O primeiro signo deve ser Capricórnio.');
        $afirmar($signos[11]->nome === 'Sagitário', 'O último signo deve ser Sagitário.');
    },
);

$executar(
    'rejeita arquivo XML inexistente',
    static function () use ($afirmarExcecao): void {
        $repositorio = new SignoXmlRepository(__DIR__ . '/arquivo-inexistente.xml');
        $afirmarExcecao(
            static fn (): array => $repositorio->obterTodos(),
            RuntimeException::class,
            'Arquivo inexistente deveria ser rejeitado',
        );
    },
);

$arquivosTemporarios = [];

$criarArquivoTemporario = static function (string $conteudo) use (&$arquivosTemporarios): string {
    $caminho = tempnam(sys_get_temp_dir(), 'zodiac-test-');

    if ($caminho === false) {
        throw new RuntimeException('Não foi possível criar arquivo temporário.');
    }

    file_put_contents($caminho, $conteudo);
    $arquivosTemporarios[] = $caminho;

    return $caminho;
};

$executar(
    'rejeita XML malformado',
    static function () use ($criarArquivoTemporario, $afirmarExcecao): void {
        $caminho = $criarArquivoTemporario('<signos><signo>');
        $repositorio = new SignoXmlRepository($caminho);
        $afirmarExcecao(
            static fn (): array => $repositorio->obterTodos(),
            RuntimeException::class,
            'XML malformado deveria ser rejeitado',
        );
    },
);

$executar(
    'rejeita XML com campos obrigatórios ausentes',
    static function () use ($criarArquivoTemporario, $afirmarExcecao): void {
        $registros = str_repeat(
            '<signo><dataInicio>01/01</dataInicio><dataFim>02/01</dataFim><signoNome>Teste</signoNome></signo>',
            12,
        );
        $caminho = $criarArquivoTemporario("<signos>{$registros}</signos>");
        $repositorio = new SignoXmlRepository($caminho);
        $afirmarExcecao(
            static fn (): array => $repositorio->obterTodos(),
            RuntimeException::class,
            'Campos ausentes deveriam ser rejeitados',
        );
    },
);

$executar(
    'rejeita XML com quantidade incorreta de signos',
    static function () use ($criarArquivoTemporario, $afirmarExcecao): void {
        $registro = '<signo><dataInicio>01/01</dataInicio><dataFim>02/01</dataFim><signoNome>Teste</signoNome><descricao>Descrição</descricao></signo>';
        $caminho = $criarArquivoTemporario("<signos>{$registro}</signos>");
        $repositorio = new SignoXmlRepository($caminho);
        $afirmarExcecao(
            static fn (): array => $repositorio->obterTodos(),
            RuntimeException::class,
            'Quantidade incorreta deveria ser rejeitada',
        );
    },
);

foreach ($arquivosTemporarios as $arquivo) {
    if (is_file($arquivo)) {
        unlink($arquivo);
    }
}

$falhas = $total - $aprovados;

echo PHP_EOL;
echo "Resumo: {$total} testes executados, {$aprovados} aprovados, {$falhas} falhos." . PHP_EOL;

exit($falhas === 0 ? 0 : 1);
