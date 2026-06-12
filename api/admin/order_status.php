<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') json_error('Method not allowed', 405);

require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) json_error('Не указан id заказа');

$body   = get_body();
$status = trim($body['status'] ?? '');

$allowed = ['Новый', 'В обработке', 'Выполнен', 'Отменён'];
if (!in_array($status, $allowed)) {
    json_error('Недопустимый статус. Разрешены: ' . implode(', ', $allowed));
}

$db   = db();
$stmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
$stmt->execute([$status, $id]);

if ($stmt->rowCount() === 0) json_error('Заказ не найден', 404);

json_success(['id' => $id, 'status' => $status]);
