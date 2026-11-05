<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/SignoService.php';

$pageTitle = 'Zodiac App | Resultado';
$signo = null;
$mensagemErro = null;
$dataInformada = '';
$dataFormatada = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $mensagemErro = 'A consulta deve ser realizada pelo formulário inicial.';
} else {
    $dataInformada = isset($_POST['data_nascimento']) && is_string($_POST['data_nascimento'])
        ? $_POST['data_nascimento']
        : '';

    try {
        $servico = new SignoService(new SignoXmlRepository());
        $signo = $servico->identificar($dataInformada);
        $dataFormatada = DateTimeImmutable::createFromFormat('!Y-m-d', $dataInformada)
            ->format('d/m/Y');
    } catch (InvalidArgumentException $erro) {
        $mensagemErro = $erro->getMessage();
    } catch (RuntimeException $erro) {
        $mensagemErro = 'Não foi possível concluir a consulta no momento.';
    }
}

require __DIR__ . '/layouts/header.php';
?>

<section class="row justify-content-center">
    <div class="col-12 col-md-9 col-lg-7">
        <?php if ($signo !== null): ?>
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <p class="text-uppercase text-success fw-semibold small mb-2">Resultado da consulta</p>
                    <h1 class="display-6 fw-bold mb-3">
                        <?= htmlspecialchars($signo->nome, ENT_QUOTES, 'UTF-8') ?>
                    </h1>
                    <p class="text-secondary mb-4">
                        Data informada:
                        <strong><?= htmlspecialchars($dataFormatada, ENT_QUOTES, 'UTF-8') ?></strong>
                    </p>
                    <div class="result-description rounded p-4 mb-4">
                        <p class="mb-0"><?= htmlspecialchars($signo->descricao, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <a class="btn btn-primary" href="index.php">Nova consulta</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning shadow-sm" role="alert">
                <h1 class="h4">Não foi possível realizar a consulta</h1>
                <p class="mb-3"><?= htmlspecialchars($mensagemErro ?? 'Ocorreu um erro inesperado.', ENT_QUOTES, 'UTF-8') ?></p>
                <a class="btn btn-outline-primary" href="index.php">Voltar ao início</a>
            </div>
        <?php endif; ?>
    </div>
</section>

</main>
<script src="assets/js/script.js"></script>
</body>
</html>
