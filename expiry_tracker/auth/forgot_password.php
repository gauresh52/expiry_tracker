<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../includes/phpmailer/SMTP.php';
require_once __DIR__ . '/../includes/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $update = $pdo->prepare("UPDATE users SET reset_token = :token, reset_expires = :expires WHERE id = :id");
        $update->execute([
            ':token' => $token,
            ':expires' => $expires,
            ':id' => $user['id']
        ]);

        $reset_link = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;

        // 📧 Send email
        $mail = new PHPMailer(true);
        try {
            // SMTP Settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'expirytracker.help@gmail.com'; // your Gmail
            $mail->Password = 'fjap sqgd woiw gmuo';  // app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender & recipient
            $mail->setFrom('yourgmail@gmail.com', 'Expiry Tracker Support');
            $mail->addAddress($email, $user['name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <h3>Hello {$user['name']},</h3>
                <p>We received a request to reset your password.</p>
                <p>Click the link below to reset it (valid for 30 minutes):</p>
                <p><a href='$reset_link'>$reset_link</a></p>
                <p>If you didn’t request this, ignore this email.</p>
                <br><p>— Expiry Tracker Team</p>
            ";

            $mail->send();
            $success = "✅ Password reset link sent to your email.";
        } catch (Exception $e) {
            $error = "❌ Email could not be sent. Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "No account found with that email address.";
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <h3 class="mb-3">Forgot Password</h3>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-3">
        <label>Enter your registered email</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary">Send Reset Link</button>
      <a href="login.php" class="btn btn-secondary ms-2">Back to Login</a>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
