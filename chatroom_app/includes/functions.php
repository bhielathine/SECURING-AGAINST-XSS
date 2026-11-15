<?php
require_once __DIR__ . '/config.php';

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

/**
 * Password rules: min 8 chars, at least one lowercase,
 * one uppercase and one number.
 * Returns a list of unmet rules (empty array = valid).
 */
function validate_password(string $password): array {
    $errors = [];
    if (strlen($password) < 8)              $errors[] = 'at least 8 characters';
    if (!preg_match('/[a-z]/', $password))  $errors[] = 'at least one lowercase letter';
    if (!preg_match('/[A-Z]/', $password))  $errors[] = 'at least one uppercase letter';
    if (!preg_match('/[0-9]/', $password))  $errors[] = 'at least one number';
    return $errors;
}
