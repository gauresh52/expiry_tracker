<?php
require_once __DIR__ . '/../config.php';

$err = '';

// 🛑 Prevent login page from being cached
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ If user already logged in, redirect them instantly (no back navigation possible)
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'admin') {
        echo "<script>window.location.replace('../admin/dashboard.php');</script>";
    } else {
        echo "<script>window.location.replace('../salesman/my_products.php');</script>";
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($pass, $user['password'])) {
        // ✅ Login success: store session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];

        // ✅ Use JavaScript replace() instead of PHP redirect
        // This prevents the browser from keeping login page in history
        if ($user['role'] === 'admin') {
            echo "<script>window.location.replace('../admin/dashboard.php');</script>";
        } else {
            echo "<script>window.location.replace('../salesman/my_products.php');</script>";
        }
        exit;
    } else {
        $err = "Invalid email or password.";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <h3 class="mb-3">Login</h3>

    <?php if ($err): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label>Email</label>
        <input name="email" type="email" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Password</label>
        <input name="password" type="password" class="form-control" required>
      </div>

      <div class="d-flex justify-content-between align-items-center">
        <button type="submit" class="btn btn-primary">Login</button>
        <button type="button" class="btn btn-outline-danger" onclick="window.location.href='forgot_password.php'">
          Forgot Password?
        </button>
      </div>
    </form>

    <hr>
    <p>Admin seed: <strong>admin@company.com</strong> / <strong>Admin@123</strong></p>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
