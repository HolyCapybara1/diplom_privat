<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') json_error('Method not allowed', 405);

require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) json_error('Не указан id пользователя');

$db = db();

// Delete user's orders first (FK constraint safe)
$stmt = $db->prepare('DELETE FROM orders WHERE user_id = ?');
$stmt->execute([$id]);

$stmt = $db->prepare('DELETE FROM users WHERE id = ?');
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) json_error('Пользователь не найден', 404);

json_success(['deleted' => $id]);
