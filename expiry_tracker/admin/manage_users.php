<?php
require_once __DIR__ . '/../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') redirect('../auth/login.php');

$err = '';
$ok = '';

// Handle block/unblock toggle
if (isset($_GET['toggle'])) {
    $uid = (int)$_GET['toggle'];

    // Fetch current status
    $stmt = $pdo->prepare("SELECT is_blocked FROM users WHERE id = :id AND role = 'salesman'");
    $stmt->execute([':id' => $uid]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $newStatus = $user['is_blocked'] ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE users SET is_blocked = :b WHERE id = :id");
        $stmt->execute([':b' => $newStatus, ':id' => $uid]);
        $ok = $newStatus ? "User has been blocked." : "User has been unblocked.";

        // Prevent re-trigger on page refresh (important)
        header("Location: manage_users.php?success=" . urlencode($ok));
        exit;
    } else {
        $err = "User not found or invalid.";
    }
}


// Handle new salesman creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['toggle'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    if (!$name || !$email || !$pass) {
        $err = "All fields required.";
    } else {
        $c = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :e");
        $c->execute([':e' => $email]);
        if ($c->fetchColumn() > 0) {
            $err = "Email already exists.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_blocked) VALUES (:n, :e, :p, 'salesman', 0)");
            $stmt->execute([':n' => $name, ':e' => $email, ':p' => $hash]);
            $ok = "Salesman account created.";
        }
    }
}

$users = $pdo->query("SELECT * FROM users WHERE role='salesman' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
require_once __DIR__ . '/../includes/header.php';
?>

<h3>Manage Salesmen</h3>
<div class="mb-3">
  <a href="dashboard.php" class="btn btn-secondary btn-sm">⬅️ Back to Dashboard</a>
</div>

<?php if ($err) echo "<div class='alert alert-danger'>{$err}</div>"; ?>
<?php if ($ok) echo "<div class='alert alert-success'>{$ok}</div>"; ?>

<div class="row">
  <div class="col-md-5">
    <h5>Create Salesman</h5>
    <form method="post">
      <div class="mb-2"><input name="name" class="form-control" placeholder="Name" required></div>
      <div class="mb-2"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
      <div class="mb-2"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
      <button class="btn btn-primary">Create</button>
    </form>
  </div>

  <div class="col-md-7">
    <h5>Salesmen List</h5>
    <table class="table table-bordered table-sm align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Created</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= $u['created_at'] ?></td>
            <td>
              <?php if ($u['is_blocked']): ?>
                <span class="badge bg-danger">Blocked</span>
              <?php else: ?>
                <span class="badge bg-success">Active</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="?toggle=<?= $u['id'] ?>" class="btn btn-sm <?= $u['is_blocked'] ? 'btn-success' : 'btn-danger' ?>">
                <?= $u['is_blocked'] ? 'Unblock' : 'Block' ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
