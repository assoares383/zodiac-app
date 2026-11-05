<?php

declare(strict_types=1);

$pageTitle = 'Zodiac App | Consulta de signo';
require __DIR__ . '/layouts/header.php';
?>

<section class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4 p-md-5">
                <h1 class="h2 mb-3">Descubra seu signo</h1>
                <p class="text-secondary mb-4">
                    Informe sua data de nascimento para consultar seu signo e conhecer sua descrição.
                </p>

                <form action="show_zodiac_sign.php" method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="data_nascimento">
                            Data de nascimento
                        </label>
                        <input
                            class="form-control form-control-lg"
                            id="data_nascimento"
                            name="data_nascimento"
                            type="date"
                            required
                            max="<?= htmlspecialchars((new DateTimeImmutable('today'))->format('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
                        >
                        <div class="form-text">A data não pode ser futura.</div>
                    </div>
                    <button class="btn btn-primary btn-lg w-100" type="submit">
                        Consultar signo
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

</main>
<script src="assets/js/script.js"></script>
</body>
</html>
