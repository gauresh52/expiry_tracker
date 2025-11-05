<?php
// show all errors during local testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config.php';

// redirect user if not logged in or not a salesman
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'salesman') {
    redirect('../auth/login.php');
}

// 🚫 Check if salesman is blocked
$stmt = $pdo->prepare("SELECT is_blocked FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$is_blocked = $stmt->fetchColumn();

if ($is_blocked) {
    require_once __DIR__ . '/../includes/header.php';
    echo "<div class='container mt-5'>
            <div class='alert alert-danger text-center'>
              🚫 Your account has been blocked by the admin. You cannot add new products.
            </div>
            <div class='text-center mt-3'>
              <a href='my_products.php' class='btn btn-primary'>📦 View My Products</a>
              <a href='../auth/logout.php' class='btn btn-secondary'>🚪 Logout</a>
            </div>
          </div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$errors = [];
$saved = false;

// handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name  = trim($_POST['product_name'] ?? '');
    $retailer_name = trim($_POST['retailer_name'] ?? '');
    $quantity      = (int)($_POST['quantity'] ?? 1);
    $expiry_date   = $_POST['expiry_date'] ?? '';
    $category      = trim($_POST['category'] ?? '');
    $remarks       = trim($_POST['remarks'] ?? '');

    // validations
    if ($product_name === '')  $errors['product_name'] = "Product name is required.";
    if ($retailer_name === '') $errors['retailer_name'] = "Retailer name is required.";
    if ($expiry_date === '')   $errors['expiry_date'] = "Expiry date is required.";

    // insert into DB
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO products 
                (product_name, retailer_name, quantity, expiry_date, category, remarks, added_by) 
                VALUES (:p, :r, :q, :e, :c, :m, :u)
            ");
            $stmt->execute([
                ':p' => $product_name,
                ':r' => $retailer_name,
                ':q' => $quantity,
                ':e' => $expiry_date,
                ':c' => $category,
                ':m' => $remarks,
                ':u' => $_SESSION['user_id']
            ]);
            $saved = true;
        } catch (PDOException $e) {
            $errors['db'] = "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
  <div class="row">
    <div class="col-md-8">
      <h3>Add Expired Product</h3>

      <?php if (isset($errors['db'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errors['db']) ?></div>
      <?php endif; ?>

      <?php if ($saved): ?>
        <div class="alert alert-success">✅ Product added successfully!</div>
      <?php endif; ?>

      <form id="productForm" method="post" class="card p-3 mt-3 shadow-sm needs-validation" novalidate>
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input name="product_name" class="form-control <?= isset($errors['product_name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['product_name'] ?? '') ?>" required>
          <div class="invalid-feedback"><?= $errors['product_name'] ?? 'Product name is required.' ?></div>
        </div>

        <div class="mb-3">
          <label class="form-label">Retailer Name</label>
          <input name="retailer_name" class="form-control <?= isset($errors['retailer_name']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['retailer_name'] ?? '') ?>" required>
          <div class="invalid-feedback"><?= $errors['retailer_name'] ?? 'Retailer name is required.' ?></div>
        </div>

        <div class="mb-3">
          <label class="form-label">Quantity</label>
          <input name="quantity" type="number" min="1" value="<?= htmlspecialchars($_POST['quantity'] ?? '1') ?>" class="form-control" required>
          <div class="invalid-feedback">Quantity is required.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Expiry Date</label>
          <input name="expiry_date" type="date" class="form-control <?= isset($errors['expiry_date']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($_POST['expiry_date'] ?? '') ?>" required>
          <div class="invalid-feedback"><?= $errors['expiry_date'] ?? 'Expiry date is required.' ?></div>
        </div>

        <div class="mb-3">
          <label class="form-label">Category</label>
          <input name="category" class="form-control" value="<?= htmlspecialchars($_POST['category'] ?? '') ?>" placeholder="Optional">
        </div>

        <div class="mb-3">
          <label class="form-label">Remarks</label>
          <textarea name="remarks" class="form-control" rows="3" placeholder="Optional"><?= htmlspecialchars($_POST['remarks'] ?? '') ?></textarea>
        </div>

        <button type="button" class="btn btn-success" id="previewBtn">Add Product</button>
      </form>
    </div>

    <div class="col-md-4">
      <h5>Quick Navigation</h5>
      <div class="d-grid gap-2 mt-2">
        <a class="btn btn-info" href="my_products.php">📦 My Products</a>
      </div>
    </div>
  </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Confirm Product Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p><strong>Product Name:</strong> <span id="c_product"></span></p>
        <p><strong>Retailer Name:</strong> <span id="c_retailer"></span></p>
        <p><strong>Quantity:</strong> <span id="c_quantity"></span></p>
        <p><strong>Expiry Date:</strong> <span id="c_expiry"></span></p>
        <p><strong>Category:</strong> <span id="c_category"></span></p>
        <p><strong>Remarks:</strong> <span id="c_remarks"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Edit</button>
        <button type="button" class="btn btn-success" id="confirmSubmit">Confirm & Submit</button>
      </div>
    </div>
  </div>
</div>

<script>
// Bootstrap inline validation
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()

// Preview modal logic
document.getElementById('previewBtn').addEventListener('click', function() {
  const form = document.getElementById('productForm');

  if (!form.checkValidity()) {
    form.classList.add('was-validated');
    return; // stop if invalid
  }

  const product = form.product_name.value.trim();
  const retailer = form.retailer_name.value.trim();
  const qty = form.quantity.value;
  const expiry = form.expiry_date.value;
  const cat = form.category.value.trim() || '-';
  const remarks = form.remarks.value.trim() || '-';

  document.getElementById('c_product').textContent = product;
  document.getElementById('c_retailer').textContent = retailer;
  document.getElementById('c_quantity').textContent = qty;
  document.getElementById('c_expiry').textContent = expiry;
  document.getElementById('c_category').textContent = cat;
  document.getElementById('c_remarks').textContent = remarks;

  const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
  modal.show();

  document.getElementById('confirmSubmit').onclick = function() {
    modal.hide();
    form.submit();
  };
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
