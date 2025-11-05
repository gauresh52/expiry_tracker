<?php
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';
$filter = $_GET['filter'] ?? 'added_on'; // Default filter

if (!$start || !$end) {
    die("Start and End date required.");
}

// ✅ Validate filter input
$allowed_filters = ['added_on', 'expiry_date'];
if (!in_array($filter, $allowed_filters)) {
    die("Invalid filter type.");
}

// ✅ Extend end date to include the full day (if column type is DATETIME)
$end_extended = date('Y-m-d 23:59:59', strtotime($end));

$stmt = $pdo->prepare("
  SELECT 
    p.id,
    p.product_name,
    p.retailer_name,
    p.quantity,
    p.expiry_date,
    p.category,
    p.remarks,
    p.added_on,
    u.name AS salesman_name
  FROM products p
  JOIN users u ON p.added_by = u.id
  WHERE p.$filter BETWEEN :start AND :end
  ORDER BY p.$filter ASC
");

// ✅ Pass full-day range
$stmt->execute([
    ':start' => $start,
    ':end'   => $end_extended
]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$data) {
    die("No records found for selected range.");
}

// ✅ CSV headers
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename=products_export.csv");

$output = fopen('php://output', 'w');
fputcsv($output, array_keys($data[0])); // Column headers

foreach ($data as $row) {
    // Format date columns for consistency
    $row['expiry_date'] = date('Y-m-d', strtotime($row['expiry_date']));
    $row['added_on'] = date('Y-m-d', strtotime($row['added_on']));
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
