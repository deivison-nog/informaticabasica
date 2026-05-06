<?php
define('DATA_DIR', __DIR__ . '/../data/');
define('BASE_URL', '');

// ── User helpers ───────────────────────────────────────────────────────────────

function loadUsers(): array {
    $file = DATA_DIR . 'users.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?? [];
}

function saveUsers(array $users): void {
    file_put_contents(DATA_DIR . 'users.json', json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function findUserByCpf(string $cpf): ?array {
    $cpf = preg_replace('/\D/', '', $cpf);
    foreach (loadUsers() as $u) {
        if ($u['cpf'] === $cpf) return $u;
    }
    return null;
}

function addUser(string $cpf, string $senha, string $nome, string $role): bool {
    $cpf = preg_replace('/\D/', '', $cpf);
    if (findUserByCpf($cpf)) return false; // already exists
    $users = loadUsers();
    $maxId = array_reduce($users, fn($carry, $u) => max($carry, $u['id'] ?? 0), 0);
    $users[] = [
        'id'    => $maxId + 1,
        'cpf'   => $cpf,
        'senha' => $senha,
        'nome'  => $nome,
        'role'  => $role,
    ];
    saveUsers($users);
    return true;
}

function deleteUser(int $id): void {
    $users = array_values(array_filter(loadUsers(), fn($u) => ($u['id'] ?? 0) !== $id));
    saveUsers($users);
}

// ── Publication helpers ────────────────────────────────────────────────────────

function loadPublicacoes(): array {
    $file = DATA_DIR . 'publicacoes.json';
    if (!file_exists($file)) return ['aulas' => [], 'questionarios' => []];
    return json_decode(file_get_contents($file), true) ?? ['aulas' => [], 'questionarios' => []];
}

function savePublicacoes(array $pub): void {
    file_put_contents(DATA_DIR . 'publicacoes.json', json_encode($pub, JSON_PRETTY_PRINT));
}

function toggleAula(int $aulaId): void {
    $pub = loadPublicacoes();
    $pub['aulas'][$aulaId] = !($pub['aulas'][$aulaId] ?? false);
    savePublicacoes($pub);
}

function toggleQuestionario(int $aulaId): void {
    $pub = loadPublicacoes();
    $pub['questionarios'][$aulaId] = !($pub['questionarios'][$aulaId] ?? false);
    savePublicacoes($pub);
}

function isAulaPublicada(int $aulaId): bool {
    $pub = loadPublicacoes();
    return (bool)($pub['aulas'][$aulaId] ?? false);
}

function isQuestionarioPublicado(int $aulaId): bool {
    $pub = loadPublicacoes();
    return (bool)($pub['questionarios'][$aulaId] ?? false);
}
