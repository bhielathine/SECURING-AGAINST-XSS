<?php
require_once __DIR__ . '/includes/functions.php';
require_guest();

$error    = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: chat.php');
        exit;
    }

    $error = 'No account matched &quot;' . h($username) . '&quot; with that password.';
}
require __DIR__ . '/includes/header.php';
?>
<section class="card narrow">
  <h1>Log in</h1>

  <?php if (isset($_GET['registered'])): ?>
    <div class="alert alert-ok">Account created. You can log in now.</div>
  <?php endif; ?>
  <?php if (isset($_GET['loggedout'])): ?>
    <div class="alert alert-ok">You have been logged out.</div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
  <?php endif; ?>

  <form method="post">
    <label for="username">Username</label>
    <input type="text" id="username" name="username"
           value="<?= h($username) ?>" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>

    <button class="btn btn-block" type="submit">Log in</button>
  </form>
  <p class="muted">No account yet? <a href="register.php">Create one</a></p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
