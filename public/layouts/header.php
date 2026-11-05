<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'Zodiac App';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Consulte seu signo zodiacal pela data de nascimento.">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="border-bottom bg-white">
    <nav class="navbar navbar-expand">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="index.php">Zodiac App 1111</a>
        </div>
    </nav>
</header>
<main class="container py-5">
