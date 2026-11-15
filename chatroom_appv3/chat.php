<?php
require_once __DIR__ . '/includes/functions.php';
require_login();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    if ($message === '') {
        $error = 'Write something before sending.';
    } elseif (strlen($message) > 2000) {
        $error = 'Messages are limited to 2000 characters.';
    } else {
        $clean_message = strip_tags($message);
        $stmt = $pdo->prepare('INSERT INTO messages (user_id, message) VALUES (?, ?)');
        $stmt->execute([$_SESSION['user_id'], $clean_message]);
        header('Location: chat.php');
        exit;
    }
}

$q = strip_tags(trim($_GET['q'] ?? ''));

if ($q !== '') {
    $stmt = $pdo->prepare(
        'SELECT m.id, m.message, m.created_at, u.username
           FROM messages m JOIN users u ON u.id = m.user_id
          WHERE m.message LIKE ?
          ORDER BY m.id DESC LIMIT 100'
    );
    $stmt->execute(['%' . $q . '%']);
} else {
    $stmt = $pdo->query(
        'SELECT m.id, m.message, m.created_at, u.username
           FROM messages m JOIN users u ON u.id = m.user_id
          ORDER BY m.id DESC LIMIT 100'
    );
}
$messages = array_reverse($stmt->fetchAll());

require __DIR__ . '/includes/header.php';
?>
<section class="card">
  <h1>Room</h1>

  <form method="get" class="search">
    <input type="text" name="q" placeholder="Search messages" value="<?= h($q) ?>">
    <button class="btn btn-ghost" type="submit">Search</button>
  </form>

  <?php if ($q !== ''): ?>
    <p class="muted">Results for: <em><?= h($q) ?></em> &middot; <a href="chat.php">Clear</a></p>
  <?php endif; ?>

  <div class="messages">
    <?php if (!$messages): ?>
      <p class="empty">No messages yet. Send the first one below.</p>
    <?php endif; ?>
    <?php foreach ($messages as $m): ?>
      <article class="msg<?= ($m['username'] === $_SESSION['username']) ? ' mine' : '' ?>">
        <div class="meta">
          <strong><?= h($m['username']) ?></strong>
          <time><?= h($m['created_at']) ?></time>
        </div>
        <div class="body"><?= nl2br(h($m['message'])) ?></div>
      </article>
    <?php endforeach; ?>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
  <?php endif; ?>

  <form method="post" class="composer">
    <textarea name="message" rows="3" placeholder="Write a comment" required></textarea>
    <button class="btn" type="submit">Send</button>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
