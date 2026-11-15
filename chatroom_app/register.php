<?php
require_once __DIR__ . '/includes/functions.php';
require_guest();

$errors   = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // [XSS-WEAK] No character restriction on usernames (only length),
    // so markup can be stored as a username.
    // FIX LATER: allow only /^[A-Za-z0-9_]{3,30}$/ and escape on output.
    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Username must be 3 to 50 characters.';
    }

    $missing = validate_password($password);
    if ($missing) {
        $errors[] = 'Password needs ' . implode(', ', $missing) . '.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'That username is already taken.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
        $stmt->execute([$username, $hash]);
        header('Location: login.php?registered=1');
        exit;
    }
}
require __DIR__ . '/includes/header.php';
?>
<section class="card narrow">
  <h1>Create account</h1>

  <?php foreach ($errors as $e): ?>
    <!-- [XSS-WEAK] error text (may include echoed input) is not escaped -->
    <div class="alert alert-error"><?php echo $e; ?></div>
  <?php endforeach; ?>

  <form method="post" id="regForm" novalidate>
    <label for="username">Username</label>
    <input type="text" id="username" name="username" value="<?php echo $username; ?>" required>

    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
    <ul class="rules" id="rules">
      <li data-rule="len">At least 8 characters</li>
      <li data-rule="lower">One lowercase letter</li>
      <li data-rule="upper">One uppercase letter</li>
      <li data-rule="num">One number</li>
    </ul>

    <label for="confirm_password">Confirm password</label>
    <input type="password" id="confirm_password" name="confirm_password" required>

    <button class="btn btn-block" type="submit">Create account</button>
  </form>
  <p class="muted">Already registered? <a href="login.php">Log in</a></p>
</section>

<script>
// Live password checklist (the server re-checks everything on submit).
const pw = document.getElementById('password');
const checks = {
  len:   v => v.length >= 8,
  lower: v => /[a-z]/.test(v),
  upper: v => /[A-Z]/.test(v),
  num:   v => /[0-9]/.test(v)
};
pw.addEventListener('input', () => {
  document.querySelectorAll('#rules li').forEach(li => {
    li.classList.toggle('ok', checks[li.dataset.rule](pw.value));
  });
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
