<?php
// ===== Климат-Инарс API Config =====
session_start();

// CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '*';
header("Access-Control-Allow-Origin: $origin");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ===== DB connection (singleton) =====
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=localhost;dbname=klimat_inars;charset=utf8mb4';
        $pdo = new PDO($dsn, 'ki_user', 'ki_password_2024', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// ===== Response helpers =====
function json_success(mixed $data, int $code = 200): never {
    http_response_code($code);
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_error(string $msg, int $code = 400): never {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// Read JSON body
function get_body(): array {
    $raw = file_get_contents('php://input');
    if (empty($raw)) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// Require authenticated user session
function require_auth(): array {
    if (empty($_SESSION['user_id'])) {
        json_error('Необходима авторизация', 401);
    }
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name']  ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'phone' => $_SESSION['user_phone'] ?? '',
    ];
}

// Require admin session
function require_admin(): void {
    if (empty($_SESSION['is_admin'])) {
        json_error('Доступ запрещён', 403);
    }
}

// Map DB row to JS-style product object
function map_product(array $row): array {
    return [
        'id'            => (int)$row['id'],
        'category'      => $row['category'],
        'categoryLabel' => $row['category_label'],
        'brand'         => $row['brand'],
        'model'         => $row['model'] ?? '',
        'name'          => $row['name'],
        'desc'          => $row['description'] ?? '',
        'price'         => (float)$row['price'],
        'oldPrice'      => $row['old_price'] !== null ? (float)$row['old_price'] : null,
        'power'         => $row['power'] ?? '',
        'area'          => $row['area']  ?? '',
        'noise'         => $row['noise'] ?? '',
        'specs'         => json_decode($row['specs'] ?? '[]', true) ?? [],
        'emoji'         => $row['emoji'] ?? '❄️',
        'badge'         => $row['badge'] ?? null,
        'badgeType'     => $row['badge_type'] ?? null,
        'features'      => json_decode($row['features'] ?? '[]', true) ?? [],
    ];
}
