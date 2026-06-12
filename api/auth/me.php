<?php
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') json_error('Method not allowed', 405);

if (!empty($_SESSION['is_admin'])) {
    json_success(['is_admin' => true]);
}

if (!empty($_SESSION['user_id'])) {
    json_success([
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name']  ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'phone' => $_SESSION['user_phone'] ?? '',
    ]);
}

json_success(null);
