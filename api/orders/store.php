<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

$body = get_body();

// user_id nullable (guests allowed)
$userId  = !empty($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

$name    = trim($body['name']    ?? '');
$phone   = trim($body['phone']   ?? '');
$email   = trim($body['email']   ?? '');
$address = trim($body['address'] ?? '');
$comment = trim($body['comment'] ?? '');
$items   = $body['items'] ?? [];
$total   = (float)($body['total'] ?? 0);

if (!$name || !$phone)   json_error('Укажите имя и телефон');
if (empty($items))       json_error('Корзина пуста');

$orderNum  = date('ymd') . rand(100, 999);
$dateStr   = date('d.m.Y H:i');
$itemsJson = json_encode($items, JSON_UNESCAPED_UNICODE);

$pdo  = db();
$stmt = $pdo->prepare('
    INSERT INTO orders (user_id, order_num, date, status, items, total, address, name, phone, email, comment, created_at)
    VALUES (?, ?, ?, \'Новый\', ?, ?, ?, ?, ?, ?, ?, NOW())
');
$stmt->execute([$userId, $orderNum, $dateStr, $itemsJson, $total, $address, $name, $phone, $email, $comment]);
$orderId = (int)$pdo->lastInsertId();

json_success([
    'id'        => $orderId,
    'order_num' => $orderNum,
    'date'      => $dateStr,
    'status'    => 'Новый',
    'total'     => $total,
], 201);
