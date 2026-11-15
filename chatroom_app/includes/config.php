<?php
// ---------------------------------------------------------------
// Configuration + DB connection + session start
// Adjust DB_USER / DB_PASS to match your local MySQL / MariaDB.
// ---------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'chatroom_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// [XSS-WEAK] Session cookie is readable from JavaScript (HttpOnly OFF),
// so a successful XSS payload can read document.cookie.
// FIX LATER: 'httponly' => true, 'samesite' => 'Lax'
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => false,
]);
session_start();

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Did you import sql/database.sql and set the credentials in includes/config.php?');
}
