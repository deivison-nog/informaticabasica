<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/functions.php';

function requireLogin(): void {
    if (empty($_SESSION['user'])) {
        header('Location: ' . rootPath() . '/index.php');
        exit;
    }
}

function requireRole(string ...$roles): void {
    requireLogin();
    if (!in_array($_SESSION['user']['role'] ?? '', $roles, true)) {
        header('Location: ' . rootPath() . '/index.php');
        exit;
    }
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function rootPath(): string {
    // Works when placed in any subfolder depth
    $depth = substr_count(str_replace('\\', '/', $_SERVER['SCRIPT_NAME']), '/') - 1;
    return str_repeat('../', max(0, $depth - 1));
}
