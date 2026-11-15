<?php
require_once __DIR__ . '/includes/functions.php';
header('Location: ' . (is_logged_in() ? 'chat.php' : 'login.php'));
exit;
