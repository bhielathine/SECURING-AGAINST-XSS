<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Chatroom</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="index.php">Chatroom</a>
  <nav>
    <?php if (is_logged_in()): ?>
      <span class="who">Signed in as <strong><?= h($_SESSION['username']) ?></strong></span>
      <a class="btn btn-ghost" href="logout.php">Log out</a>
    <?php else: ?>
      <a class="btn btn-ghost" href="login.php">Log in</a>
      <a class="btn" href="register.php">Create account</a>
    <?php endif; ?>
  </nav>
</header>
<main class="wrap">
