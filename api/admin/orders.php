<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Method not allowed', 405);
require_admin();

$pdo  = db();
$stmt = $pdo->query('
    SELECT o.*, u.name AS user_name, u.email AS user_email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
');
$rows = $stmt->fetchAll();

$orders = array_map(function ($row) {
    return [
        'id'         => (int)$row['id'],
        'order_num'  => $row['order_num'],
        'date'       => $row['date'],
        'status'     => $row['status'],
        'items'      => json_decode($row['items'] ?? '[]', true) ?? [],
        'total'      => (float)$row['total'],
        'address'    => $row['address']    ?? '',
        'name'       => $row['name']       ?? '',
        'phone'      => $row['phone']      ?? '',
        'email'      => $row['email']      ?? '',
        'comment'    => $row['comment']    ?? '',
        'user_id'    => $row['user_id'] ? (int)$row['user_id'] : null,
        'user_name'  => $row['user_name']  ?? null,
        'user_email' => $row['user_email'] ?? null,
    ];
}, $rows);

json_success($orders);
