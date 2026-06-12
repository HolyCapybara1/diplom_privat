<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);
require_admin();

$body = get_body();

$name     = trim($body['name']     ?? '');
$brand    = trim($body['brand']    ?? '');
$category = trim($body['category'] ?? 'split');
$price    = (float)($body['price'] ?? 0);

if (!$name || !$brand || $price <= 0) json_error('Заполните обязательные поля (name, brand, price)');

$catLabels = ['split' => 'Сплит-система', 'cassette' => 'Кассетный кондиционер', 'duct' => 'Канальный кондиционер', 'ventilation' => 'Вентиляция'];
$categoryLabel = $body['categoryLabel'] ?? ($catLabels[$category] ?? $category);

$specs    = json_encode($body['specs']    ?? [], JSON_UNESCAPED_UNICODE);
$features = json_encode($body['features'] ?? [], JSON_UNESCAPED_UNICODE);

$pdo  = db();
$stmt = $pdo->prepare('
    INSERT INTO products (category, category_label, brand, model, name, description, price, old_price, power, area, noise, specs, emoji, badge, badge_type, features, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
');
$stmt->execute([
    $category,
    $categoryLabel,
    $brand,
    $body['model']    ?? '',
    $name,
    $body['desc']     ?? '',
    $price,
    !empty($body['oldPrice']) ? (float)$body['oldPrice'] : null,
    $body['power']    ?? '',
    $body['area']     ?? '',
    $body['noise']    ?? '',
    $specs,
    $body['emoji']    ?? '❄️',
    !empty($body['badge'])     ? $body['badge']     : null,
    !empty($body['badgeType']) ? $body['badgeType'] : null,
    $features,
]);
$id = (int)$pdo->lastInsertId();

$stmt2 = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt2->execute([$id]);
$product = map_product($stmt2->fetch());

json_success($product, 201);
