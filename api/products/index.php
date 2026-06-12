<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Method not allowed', 405);

$db   = db();
$stmt = $db->query('SELECT * FROM products ORDER BY id ASC');
$rows = $stmt->fetchAll();

$products = array_map('map_product', $rows);
json_success($products);
