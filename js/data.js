/* Product & Service data for Klimat-Inars */
var PRODUCTS = [
  // ===== НАСТЕННЫЕ СПЛИТ-СИСТЕМЫ =====
  {
    id: 1, category: 'split', categoryLabel: 'Сплит-система',
    brand: 'Samsung', model: 'WindFree Elite AR09TXEAAWKN',
    name: 'Samsung WindFree Elite 9000 BTU',
    desc: 'Инверторная сплит-система с технологией WindFree — охлаждение без прямого потока холодного воздуха. Тихая работа, Wi-Fi управление.',
    price: 45900, oldPrice: 52000,
    power: '2.5 кВт', area: 'до 25 м²', noise: '19 дБ',
    specs: ['2.5 кВт', 'Инвертор', 'Wi-Fi', 'A+++'],
    emoji: '❄️', badge: 'Хит', badgeType: 'hot',
    features: ['Инверторный компрессор', 'Wi-Fi управление', 'Функция самоочистки', 'Фильтр PM2.5']
  },
  {
    id: 2, category: 'split', categoryLabel: 'Сплит-система',
    brand: 'Daikin', model: 'FTXB25C',
    name: 'Daikin FTXB25C 2.5 кВт',
    desc: 'Надёжная японская сплит-система серии Sensira. Простое управление, высокая эффективность, 5-летняя гарантия на компрессор.',
    price: 52500, oldPrice: null,
    power: '2.5 кВт', area: 'до 25 м²', noise: '21 дБ',
    specs: ['2.5 кВт', 'Инвертор', 'Таймер', 'A++'],
    emoji: '🌬️', badge: 'Новинка', badgeType: 'new',
    features: ['Японское качество', 'Гарантия 5 лет', 'Авто-перезапуск', 'Режим обогрева']
  },
  {
    id: 3, category: 'split', categoryLabel: 'Сплит-система',
    brand: 'Mitsubishi Electric', model: 'MSZ-LN25VG',
    name: 'Mitsubishi Electric MSZ-LN 2.5 кВт',
    desc: 'Премиальная сплит-система серии LN с современным дизайном. Ультратихая работа, 3D автоматическое управление воздухом.',
    price: 67800, oldPrice: null,
    power: '2.5 кВт', area: 'до 25 м²', noise: '19 дБ',
    specs: ['2.5 кВт', 'Инвертор', 'Wi-Fi', 'A+++'],
    emoji: '❄️', badge: null, badgeType: null,
    features: ['Премиум класс', '3D авто-поток', 'i-see sensor', 'Стильный дизайн']
  },
  {
    id: 4, category: 'split', categoryLabel: 'Сплит-система',
    brand: 'Haier', model: 'AS09TT6HRA-ND',
    name: 'Haier TIBIO 2.6 кВт',
    desc: 'Бюджетная инверторная сплит-система с хорошими характеристиками. Отличное соотношение цены и качества для небольших помещений.',
    price: 28900, oldPrice: 34000,
    power: '2.6 кВт', area: 'до 25 м²', noise: '22 дБ',
    specs: ['2.6 кВт', 'Инвертор', 'Таймер', 'A+'],
    emoji: '❄️', badge: 'Акция', badgeType: 'hot',
    features: ['Выгодная цена', 'Инвертор', 'Авто-диагностика', 'Функция обогрева']
  },
  {
    id: 5, category: 'split', categoryLabel: 'Сплит-система',
    brand: 'Panasonic', model: 'CS-TZ35WKEW',
    name: 'Panasonic Etherea 3.5 кВт',
    desc: 'Элегантная сплит-система с нано-фильтром и технологией Aerowings для равномерного распределения воздуха по всему помещению.',
    price: 78500, oldPrice: null,
    power: '3.5 кВт', area: 'до 35 м²', noise: '20 дБ',
    specs: ['3.5 кВт', 'Инвертор', 'Wi-Fi', 'A+++'],
    emoji: '🌀', badge: null, badgeType: null,
    features: ['Нано-фильтр', 'Aerowings', 'Econavi сенсор', 'Wi-Fi управление']
  },
  {
    id: 6, category: 'split', categoryLabel: 'Сплит-система',
    brand: 'LG', model: 'S12EQ',
    name: 'LG Eco Smart 3.5 кВт',
    desc: 'Современная инверторная сплит-система LG с технологией Dual Inverter. Низкое потребление энергии, тихая работа.',
    price: 38900, oldPrice: 44000,
    power: '3.5 кВт', area: 'до 35 м²', noise: '21 дБ',
    specs: ['3.5 кВт', 'Dual Inverter', 'Wi-Fi', 'A++'],
    emoji: '❄️', badge: 'Акция', badgeType: 'hot',
    features: ['Dual Inverter', 'ThinQ Wi-Fi', '10-летняя гарантия мотора', 'Авто-очистка']
  },

  // ===== КАССЕТНЫЕ СИСТЕМЫ =====
  {
    id: 7, category: 'cassette', categoryLabel: 'Кассетный кондиционер',
    brand: 'Daikin', model: 'FCAHG50A',
    name: 'Daikin кассетный 5 кВт',
    desc: 'Кассетный кондиционер для монтажа в подвесной потолок. Равномерное распределение воздуха на 4 стороны, идеален для офисов.',
    price: 89900, oldPrice: null,
    power: '5.0 кВт', area: 'до 50 м²', noise: '33 дБ',
    specs: ['5.0 кВт', 'Кассетный', '4-поток', 'A++'],
    emoji: '🏢', badge: null, badgeType: null,
    features: ['Монтаж в потолок', '4-сторонний поток', 'Инвертор', 'Авто-поворот жалюзи']
  },
  {
    id: 8, category: 'cassette', categoryLabel: 'Кассетный кондиционер',
    brand: 'Mitsubishi Electric', model: 'FDT50CR',
    name: 'Mitsubishi кассетный 5 кВт',
    desc: 'Потолочный кассетный кондиционер для коммерческих помещений. Инверторное управление, высокая надёжность.',
    price: 95400, oldPrice: null,
    power: '5.0 кВт', area: 'до 50 м²', noise: '35 дБ',
    specs: ['5.0 кВт', 'Инвертор', 'Кассетный', 'A++'],
    emoji: '🏢', badge: 'Новинка', badgeType: 'new',
    features: ['Потолочный монтаж', 'Авто-очистка фильтров', 'Байпас охлаждения', 'BACnet интеграция']
  },
  {
    id: 9, category: 'cassette', categoryLabel: 'Кассетный кондиционер',
    brand: 'Samsung', model: 'AC140RN4PKH',
    name: 'Samsung кассетный 14 кВт',
    desc: 'Мощный кассетный кондиционер для больших коммерческих пространств. 360° распределение воздуха, интеграция со Smart Things.',
    price: 145000, oldPrice: null,
    power: '14.0 кВт', area: 'до 140 м²', noise: '40 дБ',
    specs: ['14 кВт', '360° поток', 'Инвертор', 'Smart'],
    emoji: '🏗️', badge: null, badgeType: null,
    features: ['360° поток воздуха', 'SmartThings', 'Авто-рестарт', 'Крупные площади']
  },

  // ===== КАНАЛЬНЫЕ СИСТЕМЫ =====
  {
    id: 10, category: 'duct', categoryLabel: 'Канальный кондиционер',
    brand: 'Daikin', model: 'FBQ60D',
    name: 'Daikin канальный 6 кВт',
    desc: 'Канальный кондиционер для скрытого монтажа. Встраивается в вентиляционные каналы, не занимает место в помещении.',
    price: 112000, oldPrice: null,
    power: '6.0 кВт', area: 'до 60 м²', noise: '28 дБ',
    specs: ['6.0 кВт', 'Канальный', 'Инвертор', 'A++'],
    emoji: '🌫️', badge: null, badgeType: null,
    features: ['Скрытый монтаж', 'Тихая работа', 'Подключение воздуховодов', 'Гибкая установка']
  },
  {
    id: 11, category: 'duct', categoryLabel: 'Канальный кондиционер',
    brand: 'Haier', model: 'AD18SS1ERA',
    name: 'Haier канальный 5.3 кВт',
    desc: 'Канальный кондиционер с встроенным вентилятором. Подходит для торговых помещений, ресторанов, офисов.',
    price: 78500, oldPrice: 88000,
    power: '5.3 кВт', area: 'до 53 м²', noise: '30 дБ',
    specs: ['5.3 кВт', 'Канальный', 'Инвертор', 'A+'],
    emoji: '🌫️', badge: 'Акция', badgeType: 'hot',
    features: ['Встроенный вентилятор', 'Доступная цена', 'Авто-рестарт', 'Простой монтаж']
  },

  // ===== ВЕНТИЛЯЦИЯ =====
  {
    id: 12, category: 'ventilation', categoryLabel: 'Вентиляция',
    brand: 'Ballu', model: 'BKA-3.0',
    name: 'Приточная установка Ballu 3.0 кВт',
    desc: 'Компактная приточная вентиляционная установка с нагревателем. Обеспечивает подачу свежего воздуха в помещения.',
    price: 45000, oldPrice: null,
    power: '3.0 кВт', area: 'до 100 м³/ч', noise: '38 дБ',
    specs: ['300 м³/ч', 'Нагрев', 'Фильтр G4', '220В'],
    emoji: '💨', badge: null, badgeType: null,
    features: ['Подача свежего воздуха', 'Электронагреватель', 'Фильтрация G4', 'Простой монтаж']
  },
  {
    id: 13, category: 'ventilation', categoryLabel: 'Вентиляция',
    brand: 'Systemair', model: 'VR 150',
    name: 'Рекуператор Systemair VR 150',
    desc: 'Пластинчатый рекуператор тепла для эффективной вентиляции с возвратом тепловой энергии. Экономия на отоплении до 80%.',
    price: 38900, oldPrice: null,
    power: '0.14 кВт', area: '150 м³/ч', noise: '35 дБ',
    specs: ['150 м³/ч', 'КПД 80%', 'Рекуперация', '220В'],
    emoji: '♻️', badge: 'Новинка', badgeType: 'new',
    features: ['КПД рекуперации 80%', 'Экономия тепла', 'Байпас летом', 'Тихая работа']
  },
  {
    id: 14, category: 'ventilation', categoryLabel: 'Вентиляция',
    brand: 'Vents', model: 'ВУТ 150 Г',
    name: 'Рекуператор Vents ВУТ 150 Г',
    desc: 'Бытовой рекуператор с горизонтальным расположением. Компактный, простой в установке, эффективная вентиляция квартиры.',
    price: 28500, oldPrice: 32000,
    power: '0.08 кВт', area: '150 м³/ч', noise: '32 дБ',
    specs: ['150 м³/ч', 'КПД 75%', 'Компакт', '220В'],
    emoji: '🔄', badge: 'Акция', badgeType: 'hot',
    features: ['Для квартир', 'Компактный', 'Горизонтальный монтаж', 'Авто-управление']
  },
  {
    id: 15, category: 'ventilation', categoryLabel: 'Вентиляция',
    brand: 'Mitsubishi Electric', model: 'VL-100EU5-E',
    name: 'Рекуператор Mitsubishi 100 м³/ч',
    desc: 'Бытовой лопастной рекуператор с высоким КПД. Японская надёжность, тихая работа, легкий монтаж в стену.',
    price: 52000, oldPrice: null,
    power: '0.05 кВт', area: '100 м³/ч', noise: '25 дБ',
    specs: ['100 м³/ч', 'КПД 85%', 'Лопастной', '220В'],
    emoji: '🍃', badge: null, badgeType: null,
    features: ['КПД 85%', 'Монтаж в стену', 'Антибактериальный фильтр', 'Тихий 25 дБ']
  }
];

var SERVICES = [
  {
    id: 's1', emoji: '🔧', title: 'Монтаж сплит-системы',
    desc: 'Профессиональная установка настенных и мульти-сплит систем. Прокладка трасс, вакуумирование, пуско-наладка.',
    price: 'от 8 000 руб', basePrice: 8000,
    includes: ['Монтаж блоков', 'Прокладка трассы', 'Вакуумирование', 'Пуско-наладка', 'Гарантия 2 года']
  },
  {
    id: 's2', emoji: '🏗️', title: 'Монтаж канального/кассетного',
    desc: 'Установка полупромышленных и коммерческих систем кондиционирования. Проектирование воздуховодов.',
    price: 'от 25 000 руб', basePrice: 25000,
    includes: ['Проектирование', 'Монтаж блоков', 'Воздуховоды', 'Настройка', 'Гарантия 2 года']
  },
  {
    id: 's3', emoji: '🛠️', title: 'Техническое обслуживание',
    desc: 'Чистка фильтров и теплообменников, проверка давления фреона, диагностика, профилактика неисправностей.',
    price: 'от 3 500 руб', basePrice: 3500,
    includes: ['Чистка фильтров', 'Мойка блоков', 'Проверка давления', 'Диагностика', 'Акт выполнения']
  },
  {
    id: 's4', emoji: '❄️', title: 'Заправка фреоном',
    desc: 'Дозаправка или полная замена хладагента. Поиск и устранение утечек. Работаем с R-22, R-32, R-410A.',
    price: 'от 2 000 руб', basePrice: 2000,
    includes: ['Проверка утечек', 'Вакуумирование', 'Заправка фреоном', 'Проверка давления', 'Контроль работы']
  },
  {
    id: 's5', emoji: '📐', title: 'Проектирование системы',
    desc: 'Разработка проекта вентиляции и кондиционирования для жилых и коммерческих объектов. Подбор оборудования.',
    price: 'от 15 000 руб', basePrice: 15000,
    includes: ['Обследование объекта', 'Расчёт нагрузок', 'Выбор оборудования', 'Проектная документация', 'Согласование']
  },
  {
    id: 's6', emoji: '🔍', title: 'Диагностика и ремонт',
    desc: 'Профессиональная диагностика неисправностей, ремонт плат управления, замена компонентов.',
    price: 'от 1 500 руб', basePrice: 1500,
    includes: ['Выезд мастера', 'Диагностика', 'Ремонт', 'Замена деталей', 'Гарантия на ремонт']
  }
];

// Expose globally — products are loaded from API in catalog.js/admin.html
// Keeping PRODUCTS as fallback for pages that don't load from API (e.g. index.html)
window.PRODUCTS = PRODUCTS;
window.SERVICES = SERVICES;
