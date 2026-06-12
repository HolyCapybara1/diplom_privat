<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') json_error('Method not allowed', 405);

$session = require_auth();

$body  = get_body();
$name  = trim($body['name']  ?? '');
$phone = trim($body['phone'] ?? '');

if (!$name) json_error('Укажите имя');

$pdo  = db();
$stmt = $pdo->prepare('UPDATE users SET name = ?, phone = ? WHERE id = ?');
$stmt->execute([$name, $phone, $session['id']]);

// Update session
$_SESSION['user_name']  = $name;
$_SESSION['user_phone'] = $phone;

json_success([
    'id'    => $session['id'],
    'name'  => $name,
    'email' => $session['email'],
    'phone' => $phone,
]);
