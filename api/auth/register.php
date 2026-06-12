<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);

$body = get_body();
$name     = trim($body['name']     ?? '');
$email    = strtolower(trim($body['email']    ?? ''));
$phone    = trim($body['phone']    ?? '');
$password = $body['password'] ?? '';

if (!$name || !$email || !$password) json_error('Заполните обязательные поля');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('Некорректный email');
if (strlen($password) < 6) json_error('Пароль должен быть не менее 6 символов');

$db = db();

// Check duplicate email
$stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) json_error('Пользователь с таким email уже зарегистрирован');

// Insert user
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $db->prepare('INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)');
$stmt->execute([$name, $email, $phone, $hash]);
$userId = (int)$db->lastInsertId();

// Start session
$_SESSION['user_id']    = $userId;
$_SESSION['user_name']  = $name;
$_SESSION['user_email'] = $email;
$_SESSION['user_phone'] = $phone;

$user = ['id' => $userId, 'name' => $name, 'email' => $email, 'phone' => $phone];
json_success($user, 201);
