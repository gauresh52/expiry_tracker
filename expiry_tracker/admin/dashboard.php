<?php
require_once __DIR__ . '/../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    redirect('../auth/login.php');
}

// Quick stats
$total = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// Expiring within 30 days
$soon = $pdo->prepare("
    SELECT COUNT(*) FROM products 
    WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
");
$soon->execute();
$soon_count = $soon->fetchColumn();

// Submitted today
$today = $pdo->prepare("
    SELECT COUNT(*) FROM products 
    WHERE DATE(added_on) = CURDATE()
");
$today->execute();
$today_count = $today->fetchColumn();

// Latest 200 entries
$q = "
    SELECT p.*, u.name AS added_by_name 
    FROM products p 
    JOIN users u ON p.added_by = u.id 
    ORDER BY p.expiry_date ASC 
    LIMIT 200
";
$rows = $pdo->query($q)->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/../includes/header.php';
?>

<h3 class="mb-4">Admin Dashboard</h3>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card p-3 shadow-sm text-center border-0 bg-light">
      <h6 class="text-muted">Total Expiry Submitted</h6>
      <h4 class="fw-bold text-primary mb-0"><?= $total; ?></h4>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm text-center border-0 bg-light">
      <h6 class="text-muted">Expiring Within 30 Days</h6>
      <h4 class="fw-bold text-warning mb-0"><?= $soon_count; ?></h4>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card p-3 shadow-sm text-center border-0 bg-light">
      <h6 class="text-muted">Submitted Today</h6>
      <h4 class="fw-bold text-success mb-0"><?= $today_count; ?></h4>
    </div>
  </div>
</div>

<div class="mb-3">
  <a class="btn btn-primary" href="manage_users.php">👤 Manage Salesmen</a>
</div>

<!-- CSV Export Form -->
<form id="csvForm" action="export_csv.php" method="get" class="mb-4">
  <div class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label">Start Date:</label>
      <input type="date" name="start" id="startDate" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">End Date:</label>
      <input type="date" name="end" id="endDate" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Filter By:</label>
      <select name="filter" required class="form-select">
        <option value="added_on">Added Date</option>
        <option value="expiry_date">Expiry Date</option>
      </select>
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-success w-100">Download CSV</button>
    </div>
  </div>
</form>

<!-- Validation Alert -->
<div id="alertBox" class="alert alert-danger d-none"></div>

<table class="table table-bordered table-sm align-middle shadow-sm">
  <thead class="table-light">
    <tr>
      <th>Product</th>
      <th>Retailer</th>
      <th>Qty</th>
      <th>Expiry</th>
      <th>Added By</th>
      <th>Added On</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['product_name']); ?></td>
        <td><?= htmlspecialchars($r['retailer_name']); ?></td>
        <td><?= $r['quantity']; ?></td>
        <td><?= htmlspecialchars($r['expiry_date']); ?></td>
        <td><?= htmlspecialchars($r['added_by_name']); ?></td>
        <td><?= htmlspecialchars($r['added_on']); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
document.getElementById('csvForm').addEventListener('submit', function(e) {
  const start = document.getElementById('startDate').value;
  const end = document.getElementById('endDate').value;
  const alertBox = document.getElementById('alertBox');

  if (start && end) {
    const startDate = new Date(start);
    const endDate = new Date(end);

    if (startDate > endDate) {
      e.preventDefault();
      alertBox.textContent = "❌ Start date cannot be after End date. They can be the same or Start can be earlier.!";
      alertBox.classList.remove('d-none');
      return false;
    } else {
      alertBox.classList.add('d-none');
    }
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
