<?php
require_once __DIR__.'/../config.php';
if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'salesman') redirect('../auth/login.php');

$stmt = $pdo->prepare("SELECT * FROM products WHERE added_by = :uid ORDER BY added_on DESC");
$stmt->execute([':uid'=>$_SESSION['user_id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__.'/../includes/header.php';
?>
<h3>My Submitted Products</h3>
<table class="table table-striped">
  <thead><tr><th>Product</th><th>Retailer</th><th>Qty</th><th>Expiry</th><th>Added On</th></tr></thead>
  <tbody>
    <?php foreach($rows as $r): ?>
      <tr>
        <td><?php echo htmlspecialchars($r['product_name']);?></td>
        <td><?php echo htmlspecialchars($r['retailer_name']);?></td>
        <td><?php echo $r['quantity'];?></td>
        <td><?php echo $r['expiry_date'];?></td>
        <td><?php echo $r['added_on'];?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<a class="btn btn-secondary" href="add_product.php">Add New</a>
<?php require_once __DIR__.'/../includes/footer.php'; ?>
