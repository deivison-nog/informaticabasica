<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
$pageTitle = 'Admin — Adicionar Usuário';

$msg = $msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf   = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $nome  = trim($_POST['nome'] ?? '');
    $role  = $_POST['role'] ?? '';

    if (strlen($cpf) !== 11 || empty($senha) || !in_array($role, ['admin','professor','aluno'], true)) {
        $msg = 'Preencha todos os campos corretamente.';
        $msgType = 'danger';
    } elseif (!addUser($cpf, $senha, $nome ?: $cpf, $role)) {
        $msg = 'CPF já cadastrado no sistema.';
        $msgType = 'warning';
    } else {
        $msg = 'Usuário cadastrado com sucesso!';
        $msgType = 'success';
    }
}

include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-person-plus me-2"></i>Adicionar Usuário</h4>
<?php if ($msg): ?><div class="alert alert-<?= $msgType ?> py-2"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="card p-4" style="max-width:480px">
  <form method="post" novalidate>
    <div class="mb-3">
      <label class="form-label fw-semibold">CPF <span class="text-danger">*</span></label>
      <input type="text" name="cpf" id="cpfInput" class="form-control" placeholder="000.000.000-00" maxlength="14" required>
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Senha <span class="text-danger">*</span></label>
      <input type="text" name="senha" class="form-control" placeholder="Senha" required>
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Nome</label>
      <input type="text" name="nome" class="form-control" placeholder="Nome completo (opcional)">
    </div>
    <div class="mb-4">
      <label class="form-label fw-semibold">Perfil <span class="text-danger">*</span></label>
      <select name="role" class="form-select" required>
        <option value="">Selecione...</option>
        <option value="admin">Administrador</option>
        <option value="professor">Professor</option>
        <option value="aluno">Aluno</option>
      </select>
    </div>
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Salvar</button>
      <a href="usuarios.php" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
<script>
document.getElementById('cpfInput').addEventListener('input', function() {
  let v = this.value.replace(/\D/g, '').substring(0,11);
  v = v.replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2');
  this.value = v;
});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
