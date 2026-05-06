<?php
/**
 * aulas_conteudo.php
 * Arquivo completo com todo o conteúdo das 4 aulas de Informática Básica.
 * Inclui espaços para imagens claramente identificados como [IMAGEM X.Y].
 *
 * Para visualizar individualmente:  include 'aula1.php' ... 'aula4.php'
 * Este arquivo serve como referência consolidada do material didático.
 */

$aulaFiles = [
    1 => __DIR__ . '/aula1.php',
    2 => __DIR__ . '/aula2.php',
    3 => __DIR__ . '/aula3.php',
    4 => __DIR__ . '/aula4.php',
];

// If called standalone (not included by another page), render as a full page
if (basename($_SERVER['PHP_SELF']) === basename(__FILE__)):
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Informática Básica — Todo o Conteúdo</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background: #f8f9fa; }
    .lesson-content { margin-bottom: 2rem; }
    .border-dashed { border-style: dashed !important; }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3 mb-4">
  <span class="navbar-brand fw-bold"><i class="bi bi-pc-display-horizontal me-2"></i>Informática Básica — Material Completo</span>
</nav>
<div class="container">
  <div class="d-flex gap-2 mb-4 flex-wrap">
    <a href="#aula1" class="btn btn-primary btn-sm">Aula 1</a>
    <a href="#aula2" class="btn btn-info btn-sm">Aula 2</a>
    <a href="#aula3" class="btn btn-success btn-sm">Aula 3</a>
    <a href="#aula4" class="btn btn-warning btn-sm">Aula 4</a>
  </div>

  <?php foreach ($aulaFiles as $num => $file): ?>
  <div id="aula<?= $num ?>" class="card mb-5 p-4 border-dashed">
    <?php include $file; ?>
  </div>
  <?php endforeach; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
endif;
?>
