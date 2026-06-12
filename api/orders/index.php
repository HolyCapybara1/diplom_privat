<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Method not allowed', 405);

$session = require_auth();

$db   = db();
$stmt = $db->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([$session['id']]);
$rows = $stmt->fetchAll();

$orders = array_map(function ($row) {
    return [
        'id'        => (int)$row['id'],
        'order_num' => $row['order_num'],
        'date'      => $row['date'],
        'status'    => $row['status'],
        'items'     => json_decode($row['items'] ?? '[]', true) ?? [],
        'total'     => (float)$row['total'],
        'address'   => $row['address'],
        'name'      => $row['name'],
        'phone'     => $row['phone'],
        'email'     => $row['email'],
        'comment'   => $row['comment'],
    ];
}, $rows);

json_success($orders);
