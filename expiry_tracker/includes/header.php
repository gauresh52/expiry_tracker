<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Server-side cache prevention
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['login.php', 'forgot_password.php', 'reset_password.php'];

// redirect to login if not logged in and page is protected
if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: ../auth/login.php");
    exit();
}

$user_role = ucfirst($_SESSION['user_role'] ?? '');
$user_name = htmlspecialchars($_SESSION['user_name'] ?? '');

// Output HTML + bootstrap
if (!isset($no_bootstrap)) {
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<title>Expiry Tracker</title>';

    // Only include the back-navigation neutralizer JS when user is logged in
    if (isset($_SESSION['user_id'])) {
        echo '
        <script>
        (function() {
          // Push a dummy state so the login page (if in history) is not returned to
          try {
            history.replaceState(null, document.title, location.href);
            history.pushState(null, document.title, location.href);
          } catch (e) { /* ignore */ }

          // When user attempts to navigate (back/forward), re-push them to the same page
          window.addEventListener("popstate", function (e) {
            // Immediately push the same state to neutralize navigation
            try { history.pushState(null, document.title, location.href); } catch (err) {}
          });

          // When page is shown from bfcache, reload to re-check session on server
          window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
              // force reload from server, which checks session and redirects if needed
              window.location.reload();
            }
          });
        })();
        </script>
        ';
    }

    echo '</head><body><div class="container py-4">';

    // Header greeting & logout for logged-in users
    if (isset($_SESSION['user_id'])) {
        echo '<div class="d-flex justify-content-between align-items-center mb-4">';
        echo '<h5 class="text-primary mb-0">👋 Hello, ' . $user_role . ' ' . $user_name . '</h5>';
        echo '<a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>';
        echo '</div>';
    }
}
?>
