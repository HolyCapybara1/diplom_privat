<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Method not allowed', 405);

require_admin();

$db = db();

$products_count = (int)$db->query('SELECT COUNT(*) FROM products')->fetchColumn();
$users_count    = (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$orders_count   = (int)$db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$revenue        = (float)$db->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'Отменён'")->fetchColumn();
$new_orders     = (int)$db->query("SELECT COUNT(*) FROM orders WHERE status = 'Новый'")->fetchColumn();

// Orders by month (last 7 days)
$by_month = [];
for ($i = 6; $i >= 0; $i--) {
    $date  = date('Y-m-d', strtotime("-$i days"));
    $label = date('d.m', strtotime("-$i days"));
    $stmt  = $db->prepare("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = ?");
    $stmt->execute([$date]);
    $by_month[] = ['label' => $label, 'count' => (int)$stmt->fetchColumn()];
}

// Orders by category
$by_category_raw = $db->query("
    SELECT p.category, COUNT(*) as cnt
    FROM orders o
    JOIN products p ON JSON_CONTAINS(o.items, JSON_OBJECT('id', p.id))
    GROUP BY p.category
")->fetchAll();

$by_category = [];
foreach ($by_category_raw as $row) {
    $by_category[$row['category']] = (int)$row['cnt'];
}

// Fallback demo data if no orders
if (!$orders_count) {
    $by_category = ['split' => 0, 'cassette' => 0, 'duct' => 0, 'ventilation' => 0];
}

json_success([
    'products' => $products_count,
    'users'    => $users_count,
    'orders'   => $orders_count,
    'revenue'  => $revenue,
    'new_orders' => $new_orders,
    'by_month'   => $by_month,
    'by_category' => $by_category,
]);
