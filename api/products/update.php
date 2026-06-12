<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') json_error('Method not allowed', 405);

require_admin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) json_error('Не указан id товара');

$db   = db();
$stmt = $db->prepare('SELECT id FROM products WHERE id = ?');
$stmt->execute([$id]);
if (!$stmt->fetch()) json_error('Товар не найден', 404);

$body = get_body();

$catLabels = ['split' => 'Сплит-система', 'cassette' => 'Кассетный кондиционер', 'duct' => 'Канальный кондиционер', 'ventilation' => 'Вентиляция'];
$category  = trim($body['category'] ?? 'split');
$category_label = trim($body['categoryLabel'] ?? ($catLabels[$category] ?? $category));

$stmt = $db->prepare('
    UPDATE products SET
        category      = ?,
        category_label = ?,
        brand         = ?,
        model         = ?,
        name          = ?,
        description   = ?,
        price         = ?,
        old_price     = ?,
        power         = ?,
        area          = ?,
        noise         = ?,
        specs         = ?,
        emoji         = ?,
        badge         = ?,
        badge_type    = ?,
        features      = ?
    WHERE id = ?
');
$stmt->execute([
    $category,
    $category_label,
    trim($body['brand']       ?? ''),
    trim($body['model']       ?? ''),
    trim($body['name']        ?? ''),
    trim($body['desc']        ?? $body['description'] ?? ''),
    (float)($body['price']    ?? 0),
    isset($body['oldPrice']) && $body['oldPrice'] !== null && $body['oldPrice'] !== '' ? (float)$body['oldPrice'] : null,
    trim($body['power']       ?? ''),
    trim($body['area']        ?? ''),
    trim($body['noise']       ?? ''),
    json_encode($body['specs']    ?? [], JSON_UNESCAPED_UNICODE),
    trim($body['emoji']       ?? '❄️'),
    $body['badge']            ?? null,
    $body['badgeType']        ?? null,
    json_encode($body['features'] ?? [], JSON_UNESCAPED_UNICODE),
    $id,
]);

$row = $db->prepare('SELECT * FROM products WHERE id = ?');
$row->execute([$id]);
json_success(map_product($row->fetch()));
