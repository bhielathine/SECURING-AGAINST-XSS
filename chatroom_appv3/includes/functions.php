<?php
require_once __DIR__ . '/config.php';

function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_guest(): void {
    if (is_logged_in()) {
        header('Location: chat.php');
        exit;
    }
}

function validate_password(string $password): array {
    $errors = [];
    if (strlen($password) < 8)              $errors[] = 'at least 8 characters';
    if (!preg_match('/[a-z]/', $password))  $errors[] = 'at least one lowercase letter';
    if (!preg_match('/[A-Z]/', $password))  $errors[] = 'at least one uppercase letter';
    if (!preg_match('/[0-9]/', $password))  $errors[] = 'at least one number';
    return $errors;
}

function validate_username(string $u): string {
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $u)) {
        return 'Username must be 3–30 characters, letters, digits and underscores only.';
    }
    return '';
}
