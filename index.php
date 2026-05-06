<?php
session_start();
require_once __DIR__ . '/config/functions.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf   = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    $user = findUserByCpf($cpf);
    if ($user && $user['senha'] === $senha) {
        $_SESSION['user'] = $user;
        switch ($user['role']) {
            case 'admin':     header('Location: admin/index.php');     break;
            case 'professor': header('Location: professor/index.php'); break;
            case 'aluno':     header('Location: aluno/index.php');     break;
            default:          header('Location: index.php');
        }
        exit;
    }
    $erro = 'CPF ou senha inválidos.';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Informática Básica — Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); min-height: 100vh; display: flex; align-items: center; }
    .card { border: none; box-shadow: 0 8px 32px rgba(0,0,0,.18); border-radius: 16px; }
    .brand-icon { font-size: 3rem; color: #0d6efd; }
  </style>
</head>
<body>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-sm-8 col-md-5 col-lg-4">
      <div class="card p-4">
        <div class="text-center mb-3">
          <i class="bi bi-pc-display-horizontal brand-icon"></i>
          <h4 class="fw-bold mt-1">Informática Básica</h4>
          <p class="text-muted small">Plataforma de Ensino</p>
        </div>
        <?php if ($erro): ?>
          <div class="alert alert-danger py-2"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="post" novalidate>
          <div class="mb-3">
            <label class="form-label fw-semibold">CPF</label>
            <input type="text" name="cpf" class="form-control" placeholder="000.000.000-00"
                   maxlength="14" id="cpfInput" required autocomplete="username">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Senha</label>
            <div class="input-group">
              <input type="password" name="senha" class="form-control" id="senhaInput"
                     placeholder="Senha" required autocomplete="current-password">
              <button type="button" class="btn btn-outline-secondary" id="toggleSenha">
                <i class="bi bi-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100 fw-semibold">
            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
          </button>
        </form>
        <hr class="my-3">
        <p class="text-muted small text-center mb-0">
          <strong>Dados de teste:</strong><br>
          Admin: <code>000.000.000-00</code> / <code>admin123</code><br>
          Professor: <code>111.111.111-11</code> / <code>prof123</code><br>
          Aluno: <code>222.222.222-22</code> / <code>aluno123</code>
        </p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// CPF mask
document.getElementById('cpfInput').addEventListener('input', function() {
  let v = this.value.replace(/\D/g, '').substring(0, 11);
  v = v.replace(/(\d{3})(\d)/, '$1.$2');
  v = v.replace(/(\d{3})(\d)/, '$1.$2');
  v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
  this.value = v;
});
// Toggle password visibility
document.getElementById('toggleSenha').addEventListener('click', function() {
  const s = document.getElementById('senhaInput');
  const icon = document.getElementById('eyeIcon');
  if (s.type === 'password') {
    s.type = 'text'; icon.className = 'bi bi-eye-slash';
  } else {
    s.type = 'password'; icon.className = 'bi bi-eye';
  }
});
</script>
</body>
</html>
