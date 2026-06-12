<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

$body     = get_body();
$email    = trim($body['email']    ?? '');
$password = $body['password'] ?? '';

if (!$email || !$password) json_error('Заполните все поля');

// Admin special case
if ($email === 'admin' && $password === 'admin123') {
    session_regenerate_id(true);
    $_SESSION['is_admin'] = true;
    unset($_SESSION['user_id']);
    json_success(['is_admin' => true]);
}

// Regular user login
$db   = db();
$stmt = $db->prepare('SELECT id, name, email, phone, password FROM users WHERE email = ?');
$stmt->execute([strtolower($email)]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    json_error('Неверный email или пароль', 401);
}

session_regenerate_id(true);
$_SESSION['user_id']    = (int)$user['id'];
$_SESSION['user_name']  = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_phone'] = $user['phone'];
unset($_SESSION['is_admin']);

json_success([
    'id'    => (int)$user['id'],
    'name'  => $user['name'],
    'email' => $user['email'],
    'phone' => $user['phone'],
]);
