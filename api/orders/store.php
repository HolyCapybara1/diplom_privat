<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

$body = get_body();

$user_id = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

$name    = trim($body['name']    ?? '');
$phone   = trim($body['phone']   ?? '');
$email   = trim($body['email']   ?? '');
$address = trim($body['address'] ?? '');
$comment = trim($body['comment'] ?? '');
$items   = $body['items']  ?? [];
$total   = (float)($body['total'] ?? 0);

if (!$name || !$phone || empty($items)) json_error('Заполните обязательные поля');

$order_num = date('ymd') . rand(100, 999);
$date      = date('d.m.Y H:i');

$db   = db();
$stmt = $db->prepare('
    INSERT INTO orders (user_id, order_num, date, status, items, total, address, name, phone, email, comment)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');
$stmt->execute([
    $user_id,
    $order_num,
    $date,
    'Новый',
    json_encode($items, JSON_UNESCAPED_UNICODE),
    $total,
    $address,
    $name,
    $phone,
    $email,
    $comment,
]);

$id = (int)$db->lastInsertId();

json_success([
    'id'        => $id,
    'order_num' => $order_num,
    'date'      => $date,
    'status'    => 'Новый',
], 201);
