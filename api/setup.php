<?php
/* ===== Климат-Инжиниринг — One-time DB Setup ===== */
header('Content-Type: text/html; charset=utf-8');

$host   = 'localhost';
$dbname = 'klimat_inars';
$user   = 'ki_user';
$pass   = 'ki_password_2024';

$log = [];
$errors = [];

function ok($msg) { global $log; $log[] = "✅ $msg"; }
function fail($msg) { global $errors; $errors[] = "❌ $msg"; }

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    ok("Подключение к БД klimat_inars успешно");
} catch (PDOException $e) {
    fail("Ошибка подключения: " . $e->getMessage());
    goto output;
}

// Create tables
$tables = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name       VARCHAR(255)    NOT NULL,
    email      VARCHAR(255)    NOT NULL UNIQUE,
    phone      VARCHAR(50)     DEFAULT NULL,
    password   VARCHAR(255)    NOT NULL,
    created_at TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    category       VARCHAR(50)     NOT NULL,
    category_label VARCHAR(100)    NOT NULL,
    brand          VARCHAR(100)    NOT NULL,
    model          VARCHAR(100)    DEFAULT NULL,
    name           VARCHAR(255)    NOT NULL,
    description    TEXT            DEFAULT NULL,
    price          DECIMAL(12,2)   NOT NULL,
    old_price      DECIMAL(12,2)   DEFAULT NULL,
    power          VARCHAR(50)     DEFAULT NULL,
    area           VARCHAR(50)     DEFAULT NULL,
    noise          VARCHAR(50)     DEFAULT NULL,
    specs          JSON            DEFAULT NULL,
    emoji          VARCHAR(20)     DEFAULT '❄️',
    badge          VARCHAR(50)     DEFAULT NULL,
    badge_type     VARCHAR(50)     DEFAULT NULL,
    features       JSON            DEFAULT NULL,
    created_at     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
    id         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    user_id    INT UNSIGNED    DEFAULT NULL,
    order_num  VARCHAR(20)     NOT NULL,
    date       VARCHAR(20)     NOT NULL,
    status     VARCHAR(50)     NOT NULL DEFAULT 'Новый',
    items      JSON            NOT NULL,
    total      DECIMAL(12,2)   NOT NULL DEFAULT 0,
    address    VARCHAR(500)    DEFAULT NULL,
    name       VARCHAR(255)    NOT NULL,
    phone      VARCHAR(50)     NOT NULL,
    email      VARCHAR(255)    DEFAULT NULL,
    comment    TEXT            DEFAULT NULL,
    created_at TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

foreach (explode(';', $tables) as $sql) {
    $sql = trim($sql);
    if (!$sql) continue;
    try {
        $pdo->exec($sql);
        preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $sql, $m);
        if (!empty($m[1])) ok("Таблица {$m[1]} создана / уже существует");
    } catch (PDOException $e) {
        fail("SQL error: " . $e->getMessage());
    }
}

// Seed products if table is empty
$count = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
if ($count > 0) {
    ok("Товары уже есть в БД ($count шт.) — пропуск заполнения");
} else {
    $products = [
        [1,'split','Сплит-система','Samsung','WindFree Elite AR09TXEAAWKN','Samsung WindFree Elite 9000 BTU','Инверторная сплит-система с технологией WindFree — охлаждение без прямого потока холодного воздуха. Тихая работа, Wi-Fi управление.',45900,52000,'2.5 кВт','до 25 м²','19 дБ','["2.5 кВт","Инвертор","Wi-Fi","A+++"]','❄️','Хит','hot','["Инверторный компрессор","Wi-Fi управление","Функция самоочистки","Фильтр PM2.5"]'],
        [2,'split','Сплит-система','Daikin','FTXB25C','Daikin FTXB25C 2.5 кВт','Надёжная японская сплит-система серии Sensira. Простое управление, высокая эффективность, 5-летняя гарантия на компрессор.',52500,null,'2.5 кВт','до 25 м²','21 дБ','["2.5 кВт","Инвертор","Таймер","A++"]','🌬️','Новинка','new','["Японское качество","Гарантия 5 лет","Авто-перезапуск","Режим обогрева"]'],
        [3,'split','Сплит-система','Mitsubishi Electric','MSZ-LN25VG','Mitsubishi Electric MSZ-LN 2.5 кВт','Премиальная сплит-система серии LN с современным дизайном. Ультратихая работа, 3D автоматическое управление воздухом.',67800,null,'2.5 кВт','до 25 м²','19 дБ','["2.5 кВт","Инвертор","Wi-Fi","A+++"]','❄️',null,null,'["Премиум класс","3D авто-поток","i-see sensor","Стильный дизайн"]'],
        [4,'split','Сплит-система','Haier','AS09TT6HRA-ND','Haier TIBIO 2.6 кВт','Бюджетная инверторная сплит-система с хорошими характеристиками. Отличное соотношение цены и качества для небольших помещений.',28900,34000,'2.6 кВт','до 25 м²','22 дБ','["2.6 кВт","Инвертор","Таймер","A+"]','❄️','Акция','hot','["Выгодная цена","Инвертор","Авто-диагностика","Функция обогрева"]'],
        [5,'split','Сплит-система','Panasonic','CS-TZ35WKEW','Panasonic Etherea 3.5 кВт','Элегантная сплит-система с нано-фильтром и технологией Aerowings для равномерного распределения воздуха по всему помещению.',78500,null,'3.5 кВт','до 35 м²','20 дБ','["3.5 кВт","Инвертор","Wi-Fi","A+++"]','🌀',null,null,'["Нано-фильтр","Aerowings","Econavi сенсор","Wi-Fi управление"]'],
        [6,'split','Сплит-система','LG','S12EQ','LG Eco Smart 3.5 кВт','Современная инверторная сплит-система LG с технологией Dual Inverter. Низкое потребление энергии, тихая работа.',38900,44000,'3.5 кВт','до 35 м²','21 дБ','["3.5 кВт","Dual Inverter","Wi-Fi","A++"]','❄️','Акция','hot','["Dual Inverter","ThinQ Wi-Fi","10-летняя гарантия мотора","Авто-очистка"]'],
        [7,'cassette','Кассетный кондиционер','Daikin','FCAHG50A','Daikin кассетный 5 кВт','Кассетный кондиционер для монтажа в подвесной потолок. Равномерное распределение воздуха на 4 стороны, идеален для офисов.',89900,null,'5.0 кВт','до 50 м²','33 дБ','["5.0 кВт","Кассетный","4-поток","A++"]','🏢',null,null,'["Монтаж в потолок","4-сторонний поток","Инвертор","Авто-поворот жалюзи"]'],
        [8,'cassette','Кассетный кондиционер','Mitsubishi Electric','FDT50CR','Mitsubishi кассетный 5 кВт','Потолочный кассетный кондиционер для коммерческих помещений. Инверторное управление, высокая надёжность.',95400,null,'5.0 кВт','до 50 м²','35 дБ','["5.0 кВт","Инвертор","Кассетный","A++"]','🏢','Новинка','new','["Потолочный монтаж","Авто-очистка фильтров","Байпас охлаждения","BACnet интеграция"]'],
        [9,'cassette','Кассетный кондиционер','Samsung','AC140RN4PKH','Samsung кассетный 14 кВт','Мощный кассетный кондиционер для больших коммерческих пространств. 360° распределение воздуха, интеграция со Smart Things.',145000,null,'14.0 кВт','до 140 м²','40 дБ','["14 кВт","360° поток","Инвертор","Smart"]','🏗️',null,null,'["360° поток воздуха","SmartThings","Авто-рестарт","Крупные площади"]'],
        [10,'duct','Канальный кондиционер','Daikin','FBQ60D','Daikin канальный 6 кВт','Канальный кондиционер для скрытого монтажа. Встраивается в вентиляционные каналы, не занимает место в помещении.',112000,null,'6.0 кВт','до 60 м²','28 дБ','["6.0 кВт","Канальный","Инвертор","A++"]','🌫️',null,null,'["Скрытый монтаж","Тихая работа","Подключение воздуховодов","Гибкая установка"]'],
        [11,'duct','Канальный кондиционер','Haier','AD18SS1ERA','Haier канальный 5.3 кВт','Канальный кондиционер с встроенным вентилятором. Подходит для торговых помещений, ресторанов, офисов.',78500,88000,'5.3 кВт','до 53 м²','30 дБ','["5.3 кВт","Канальный","Инвертор","A+"]','🌫️','Акция','hot','["Встроенный вентилятор","Доступная цена","Авто-рестарт","Простой монтаж"]'],
        [12,'ventilation','Вентиляция','Ballu','BKA-3.0','Приточная установка Ballu 3.0 кВт','Компактная приточная вентиляционная установка с нагревателем. Обеспечивает подачу свежего воздуха в помещения.',45000,null,'3.0 кВт','до 100 м³/ч','38 дБ','["300 м³/ч","Нагрев","Фильтр G4","220В"]','💨',null,null,'["Подача свежего воздуха","Электронагреватель","Фильтрация G4","Простой монтаж"]'],
        [13,'ventilation','Вентиляция','Systemair','VR 150','Рекуператор Systemair VR 150','Пластинчатый рекуператор тепла для эффективной вентиляции с возвратом тепловой энергии. Экономия на отоплении до 80%.',38900,null,'0.14 кВт','150 м³/ч','35 дБ','["150 м³/ч","КПД 80%","Рекуперация","220В"]','♻️','Новинка','new','["КПД рекуперации 80%","Экономия тепла","Байпас летом","Тихая работа"]'],
        [14,'ventilation','Вентиляция','Vents','ВУТ 150 Г','Рекуператор Vents ВУТ 150 Г','Бытовой рекуператор с горизонтальным расположением. Компактный, простой в установке, эффективная вентиляция квартиры.',28500,32000,'0.08 кВт','150 м³/ч','32 дБ','["150 м³/ч","КПД 75%","Компакт","220В"]','🔄','Акция','hot','["Для квартир","Компактный","Горизонтальный монтаж","Авто-управление"]'],
        [15,'ventilation','Вентиляция','Mitsubishi Electric','VL-100EU5-E','Рекуператор Mitsubishi 100 м³/ч','Бытовой лопастной рекуператор с высоким КПД. Японская надёжность, тихая работа, легкий монтаж в стену.',52000,null,'0.05 кВт','100 м³/ч','25 дБ','["100 м³/ч","КПД 85%","Лопастной","220В"]','🍃',null,null,'["КПД 85%","Монтаж в стену","Антибактериальный фильтр","Тихий 25 дБ"]'],
    ];

    $sql = 'INSERT IGNORE INTO products (id, category, category_label, brand, model, name, description, price, old_price, power, area, noise, specs, emoji, badge, badge_type, features) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $stmt = $pdo->prepare($sql);
    $inserted = 0;
    foreach ($products as $p) {
        try {
            $stmt->execute($p);
            $inserted++;
        } catch (PDOException $e) {
            fail("Ошибка вставки товара {$p[5]}: " . $e->getMessage());
        }
    }
    ok("Добавлено $inserted товаров в каталог");
}

output:
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Setup — Климат-Инжиниринг</title>
<style>
  body { font-family: monospace; background: #1a1a2e; color: #eee; padding: 2rem; }
  h1 { color: #3498db; }
  .ok { color: #2ecc71; }
  .err { color: #e74c3c; }
  .box { background: #16213e; padding: 1.5rem; border-radius: 8px; margin: 1rem 0; }
  a { color: #3498db; }
</style>
</head>
<body>
<h1>Климат-Инжиниринг — Database Setup</h1>
<div class="box">
<?php foreach ($log as $line): ?>
  <div class="ok"><?= htmlspecialchars($line) ?></div>
<?php endforeach; ?>
<?php foreach ($errors as $line): ?>
  <div class="err"><?= htmlspecialchars($line) ?></div>
<?php endforeach; ?>
</div>
<?php if (empty($errors)): ?>
<p class="ok"><b>✅ Всё готово! База данных инициализирована.</b></p>
<p><a href="products/index.php">Проверить API товаров →</a></p>
<?php else: ?>
<p class="err"><b>❌ Есть ошибки. Проверьте настройки подключения к БД.</b></p>
<?php endif; ?>
</body>
</html>
