<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/functions.php';

function requireLogin(): void {
    if (empty($_SESSION['user'])) {
        header('Location: ' . rootPath() . 'index.php');
        exit;
    }
}

function requireRole(string ...$roles): void {
    requireLogin();
    if (!in_array($_SESSION['user']['role'] ?? '', $roles, true)) {
        header('Location: ' . rootPath() . 'index.php');
        exit;
    }
}

function currentUser(): ?array {
    return $_SESSION['user'] ?? null;
}

function rootPath(): string {
    // Returns absolute URL path to the app root, works for both root and subdirectory deployments.
    // All authenticated pages live exactly one directory deep inside the app
    // (admin/, professor/, aluno/). Pages at app root depth (index.php, logout.php) need
    // their own directory as the base.
    $uri   = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    $parts = explode('/', ltrim($uri, '/'));
    array_pop($parts); // remove filename

    // If the last directory segment is a known role folder, pop it to reach the app root.
    $knownDirs = ['admin', 'professor', 'aluno'];
    if (!empty($parts) && in_array(end($parts), $knownDirs, true)) {
        array_pop($parts);
    }

    if (empty($parts)) {
        return '/';
    }
    return '/' . implode('/', $parts) . '/';
}
