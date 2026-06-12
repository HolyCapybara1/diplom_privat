<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

require_admin();

$body = get_body();

$name          = trim($body['name']          ?? '');
$brand         = trim($body['brand']         ?? '');
$category      = trim($body['category']      ?? 'split');
$category_label = trim($body['categoryLabel'] ?? '');
$model         = trim($body['model']         ?? '');
$description   = trim($body['desc']          ?? $body['description'] ?? '');
$price         = (float)($body['price']      ?? 0);
$old_price     = isset($body['oldPrice']) && $body['oldPrice'] !== null && $body['oldPrice'] !== ''
                    ? (float)$body['oldPrice'] : null;
$power         = trim($body['power']         ?? '');
$area          = trim($body['area']          ?? '');
$noise         = trim($body['noise']         ?? '');
$emoji         = trim($body['emoji']         ?? '❄️');
$badge         = $body['badge']              ?? null;
$badge_type    = $body['badgeType']          ?? null;
$specs         = json_encode($body['specs']    ?? [], JSON_UNESCAPED_UNICODE);
$features      = json_encode($body['features'] ?? [], JSON_UNESCAPED_UNICODE);

if (!$name || !$brand || !$price) json_error('Заполните обязательные поля (name, brand, price)');

$catLabels = ['split' => 'Сплит-система', 'cassette' => 'Кассетный кондиционер', 'duct' => 'Канальный кондиционер', 'ventilation' => 'Вентиляция'];
if (!$category_label) $category_label = $catLabels[$category] ?? $category;

$db   = db();
$stmt = $db->prepare('
    INSERT INTO products
        (category, category_label, brand, model, name, description, price, old_price, power, area, noise, specs, emoji, badge, badge_type, features)
    VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
');
$stmt->execute([
    $category, $category_label, $brand, $model, $name, $description,
    $price, $old_price, $power, $area, $noise, $specs, $emoji, $badge, $badge_type, $features
]);

$id  = (int)$db->lastInsertId();
$row = $db->prepare('SELECT * FROM products WHERE id = ?');
$row->execute([$id]);
$product = map_product($row->fetch());

json_success($product, 201);
