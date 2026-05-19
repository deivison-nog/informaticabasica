<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
$pageTitle = 'Admin — Usuários';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $myId = (int)($_SESSION['user']['id'] ?? 0);
    $delId = (int)$_POST['delete_id'];
    if ($delId !== $myId) {
        deleteUser($delId);
        $msg = 'Usuário removido com sucesso.';
    } else {
        $msg = 'Não é possível remover o próprio usuário.';
    }
}

$users = loadUsers();
$roleName = ['admin' => 'Administrador', 'professor' => 'Professor', 'aluno' => 'Aluno'];
$roleBadge = ['admin' => 'danger', 'professor' => 'primary', 'aluno' => 'success'];
include __DIR__ . '/../includes/header.php';
?>
<h4 class="fw-bold mb-4"><i class="bi bi-people me-2"></i>Gerenciar Usuários</h4>
<?php if ($msg): ?><div class="alert alert-info py-2"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="d-flex justify-content-end mb-3">
  <a href="add_user.php" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Novo Usuário</a>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-dark">
        <tr><th>#</th><th>CPF</th><th>Nome</th><th>Perfil</th><th>Ação</th></tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><code><?= htmlspecialchars(preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $u['cpf'])) ?></code></td>
          <td><?= htmlspecialchars($u['nome']) ?></td>
          <td><span class="badge bg-<?= $roleBadge[$u['role']] ?? 'secondary' ?>"><?= $roleName[$u['role']] ?? $u['role'] ?></span></td>
          <td>
            <?php if ((int)$u['id'] !== (int)($_SESSION['user']['id'] ?? 0)): ?>
            <form method="post" class="d-inline" onsubmit="return confirm('Remover usuário?')">
              <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
              <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
            </form>
            <?php else: ?>
            <span class="text-muted small">você</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
