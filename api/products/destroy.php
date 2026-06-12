<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') json_error('Method not allowed', 405);
require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) json_error('Не указан id товара');

$pdo  = db();
$stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) json_error('Товар не найден', 404);

json_success(['deleted' => $id]);
