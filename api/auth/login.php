<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

$body     = get_body();
$email    = trim($body['email']    ?? '');
$password = $body['password'] ?? '';

if (!$email || !$password) json_error('Заполните все поля');

// Admin special case
if ($email === 'admin' && $password === 'admin123') {
    $_SESSION['is_admin'] = true;
    json_success(['is_admin' => true]);
}

// Regular user login
$pdo  = db();
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
$stmt->execute([strtolower($email)]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    json_error('Неверный email или пароль', 401);
}

// Start session
$_SESSION['user_id']    = (int)$user['id'];
$_SESSION['user_name']  = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_phone'] = $user['phone'] ?? '';

$result = [
    'id'    => (int)$user['id'],
    'name'  => $user['name'],
    'email' => $user['email'],
    'phone' => $user['phone'] ?? '',
];
json_success($result);
