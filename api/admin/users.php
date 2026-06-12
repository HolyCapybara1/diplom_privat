<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Method not allowed', 405);

require_admin();

$db   = db();
$stmt = $db->query('
    SELECT u.id, u.name, u.email, u.phone, u.created_at,
           COUNT(o.id) AS orders_count,
           COALESCE(SUM(o.total), 0) AS orders_total
    FROM users u
    LEFT JOIN orders o ON o.user_id = u.id
    GROUP BY u.id
    ORDER BY u.created_at DESC
');
$rows = $stmt->fetchAll();

$users = array_map(function ($row) {
    return [
        'id'           => (int)$row['id'],
        'name'         => $row['name'],
        'email'        => $row['email'],
        'phone'        => $row['phone'],
        'created_at'   => $row['created_at'],
        'orders_count' => (int)$row['orders_count'],
        'orders_total' => (float)$row['orders_total'],
    ];
}, $rows);

json_success($users);
