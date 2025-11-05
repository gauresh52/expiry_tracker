<?php
require_once __DIR__ . '/../config.php';
$token = $_GET['token'] ?? '';
$error = $success = '';

if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = :token AND reset_expires > NOW() LIMIT 1");
    $stmt->execute([':token' => $token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "Invalid or expired token.";
    }
} else {
    $error = "Missing token.";
}

// Handle password reset form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($user['id'])) {
    $newpass = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (strlen($newpass) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($newpass !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hash = password_hash($newpass, PASSWORD_DEFAULT);
        $upd = $pdo->prepare("UPDATE users SET password = :pass, reset_token = NULL, reset_expires = NULL WHERE id = :id");
        $upd->execute([':pass' => $hash, ':id' => $user['id']]);
        $success = "Password updated successfully. You can now <a href='login.php'>login</a>.";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <h3 class="mb-3">Reset Password</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!$success && isset($user['id'])): ?>
      <form method="post">
        <div class="mb-3">
          <label>New Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Confirm Password</label>
          <input type="password" name="confirm" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Update Password</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
