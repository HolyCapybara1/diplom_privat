<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

$body = get_body();
$name     = trim($body['name']     ?? '');
$email    = strtolower(trim($body['email']    ?? ''));
$phone    = trim($body['phone']    ?? '');
$password = $body['password'] ?? '';

// Validation
if (!$name)                             json_error('Укажите имя');
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('Укажите корректный email');
if (strlen($password) < 6)             json_error('Пароль должен быть не менее 6 символов');

$pdo = db();

// Check duplicate email
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    json_error('Пользователь с таким email уже зарегистрирован');
}

// Insert user
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, NOW())');
$stmt->execute([$name, $email, $phone, $hash]);
$userId = (int)$pdo->lastInsertId();

// Start session
$_SESSION['user_id']    = $userId;
$_SESSION['user_name']  = $name;
$_SESSION['user_email'] = $email;
$_SESSION['user_phone'] = $phone;

$user = ['id' => $userId, 'name' => $name, 'email' => $email, 'phone' => $phone];
json_success($user, 201);
