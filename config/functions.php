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

// ── Score helpers ──────────────────────────────────────────────────────────────

function loadScores(): array {
    $file = DATA_DIR . 'scores.json';
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?? [];
}

function saveScores(array $scores): void {
    file_put_contents(DATA_DIR . 'scores.json', json_encode($scores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Save $acertos for $cpf on quiz $aulaId, keeping the best (highest) score.
 */
function saveBestScore(string $cpf, int $aulaId, int $acertos, int $total): void {
    $scores = loadScores();
    $prev = $scores[$cpf][$aulaId]['acertos'] ?? -1;
    if ($acertos > $prev) {
        $scores[$cpf][$aulaId] = ['acertos' => $acertos, 'total' => $total];
        saveScores($scores);
    }
}

/**
 * Returns ranked list of alunos with total best-score points across all quizzes.
 * Each entry: ['nome' => ..., 'cpf' => ..., 'pontos' => ..., 'detalhe' => [...]]
 */
function getRanking(): array {
    $scores = loadScores();
    $users  = array_filter(loadUsers(), fn($u) => $u['role'] === 'aluno');
    $ranking = [];
    foreach ($users as $u) {
        $cpf    = $u['cpf'];
        $pontos = 0;
        $detalhe = [];
        for ($a = 1; $a <= 4; $a++) {
            $entry = $scores[$cpf][$a] ?? null;
            $detalhe[$a] = $entry;
            if ($entry) {
                $pontos += $entry['acertos'];
            }
        }
        $ranking[] = [
            'nome'    => $u['nome'],
            'cpf'     => $cpf,
            'pontos'  => $pontos,
            'detalhe' => $detalhe,
        ];
    }
    usort($ranking, fn($a, $b) => $b['pontos'] <=> $a['pontos']);
    return $ranking;
}

/**
 * Sanitize HTML used in editable slides to reduce XSS/code-injection risks.
 */
function sanitizeSlideOverrideHtml(string $html): string {
    $html = preg_replace('/<\?(?:php|=)?[\s\S]*?\?>/i', '', $html);

    if (!class_exists('DOMDocument')) {
        return trim($html);
    }

    $allowedTags = [
        'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'strong', 'em', 'ul', 'ol', 'li',
        'table', 'thead', 'tbody', 'tr', 'th', 'td', 'figure', 'figcaption', 'img', 'span',
        'i', 'br', 'hr', 'small', 'code', 'a'
    ];
    $allowedAttrs = ['class', 'id', 'src', 'alt', 'href', 'title'];

    $prev = libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);

    $all = $dom->getElementsByTagName('*');
    $nodes = [];
    foreach ($all as $n) $nodes[] = $n; // avoid live NodeList issues during mutation

    foreach ($nodes as $node) {
        $tag = strtolower($node->nodeName);
        if (!in_array($tag, $allowedTags, true)) {
            $text = $dom->createTextNode($node->textContent ?? '');
            if ($node->parentNode) {
                $node->parentNode->replaceChild($text, $node);
            }
            continue;
        }

        if ($node->hasAttributes()) {
            $remove = [];
            foreach ($node->attributes as $attr) {
                $name = strtolower($attr->name);
                $value = trim($attr->value);
                $disallowedAttr = !in_array($name, $allowedAttrs, true) || str_starts_with($name, 'on');
                $unsafeLink = in_array($name, ['href', 'src'], true) && preg_match('/^(javascript:|data:)/i', $value);
                if ($disallowedAttr || $unsafeLink) {
                    $remove[] = $attr->name;
                }
            }
            foreach ($remove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }
    }

    return trim($dom->saveHTML() ?: '');
}
