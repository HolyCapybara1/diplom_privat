<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Method not allowed', 405);
require_admin();

$pdo = db();

$products = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$users    = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$orders   = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$revenue  = (float)$pdo->query('SELECT COALESCE(SUM(total), 0) FROM orders')->fetchColumn();
$newOrders = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Новый'")->fetchColumn();

// Orders by month (last 7 months)
$stmt = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS cnt
    FROM orders
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
    GROUP BY month ORDER BY month ASC
");
$byMonth = $stmt->fetchAll();

// Orders by category (based on items JSON)
$catRows = $pdo->query("SELECT items FROM orders WHERE items IS NOT NULL")->fetchAll();
$byCat   = ['split' => 0, 'cassette' => 0, 'duct' => 0, 'ventilation' => 0];

// Get product names → category mapping
$prodMap = [];
$allProds = $pdo->query("SELECT name, category FROM products")->fetchAll();
foreach ($allProds as $p) {
    $prodMap[$p['name']] = $p['category'];
}

foreach ($catRows as $row) {
    $items = json_decode($row['items'], true) ?? [];
    foreach ($items as $item) {
        $cat = $prodMap[$item['name'] ?? ''] ?? null;
        if ($cat && isset($byCat[$cat])) {
            $byCat[$cat] += (int)($item['qty'] ?? 1);
        }
    }
}

json_success([
    'products'   => $products,
    'users'      => $users,
    'orders'     => $orders,
    'revenue'    => $revenue,
    'new_orders' => $newOrders,
    'by_month'   => $byMonth,
    'by_category'=> $byCat,
]);
