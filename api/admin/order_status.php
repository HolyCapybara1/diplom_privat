<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') json_error('Method not allowed', 405);
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) json_error('Не указан id заказа');

$body   = get_body();
$status = trim($body['status'] ?? '');

$allowed = ['Новый', 'В обработке', 'Выполнен', 'Отменён'];
if (!in_array($status, $allowed, true)) {
    json_error('Недопустимый статус. Допустимые: ' . implode(', ', $allowed));
}

$pdo  = db();
$stmt = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
$stmt->execute([$status, $id]);

if ($stmt->rowCount() === 0) {
    $check = $pdo->prepare('SELECT id FROM orders WHERE id = ?');
    $check->execute([$id]);
    if (!$check->fetch()) json_error('Заказ не найден', 404);
}

json_success(['id' => $id, 'status' => $status]);
