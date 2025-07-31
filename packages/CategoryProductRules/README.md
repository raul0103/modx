# Компонент: CategoryProductRules

## Описание

Выборки товаров в небольших категориях, похожие товары.
Условия выборок настраиваются через админку.

## 📁 Структура проекта

```
components/
└── CategoryProductRules/
    ├── app/            # Исходники фронтенда (React + Vite)
    ├── controllers/    # Инициализация страницы в админке MODX
    ├── actions/        # Экшены для работы с данными в базе
        └── mgr         # Для доступа к mgr контексту проверяется авторизация
        └── web
    ├── templates/      # Сборка фронта (index.html, assets)
    └── action.php      # API-обработчик для фронта
```

---

## 🚀 Production

1. Для деплоя залейте каталог в `{core_path}components/CategoryProductRules/`
2. Создайте записи в "Пространство имен"

```code
Название
CategoryProductRules

Путь к ядру
{core_path}components/CategoryProductRules/

Путь к активам
{assets_path}components/CategoryProductRules/
```

3. Проверить что в action.php `const PRODUCTION = true;`
4. С app/ запустить `npm run build` - Все необходимые файлы попадут в assets/components/CategoryProductRules
5. Запустить миграции `database/migrations/table_name.php` (Пока через консоль. Не придумал как упростить установку)

Если будут ошибки проверь app/.env.production

---

## Вывод на странице

1. Создаем сниппет getRules.php

```php
<?php

$category_id = $modx->resource->id;
$table_prefix = $modx->getOption('table_prefix');

try {
    $res = $modx->query("SELECT * FROM {$table_prefix}catprod_rules WHERE category_id = $category_id");
    $data = $res->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $rules = json_decode($data['rules'], true);
        return [
            'parents' => implode(',', $rules['parents']),
            'options' => str_replace(["{", "}"], ["{ ", " }"], json_encode($rules['options'], JSON_UNESCAPED_UNICODE))
        ];
    } else {
        return null;
    }
} catch (Exception $e) {
}

```

2. Выводим в чанке

```php
{if $rules = "@FILE _modules/category-product-rules/snippets/getRules.php" | snippet}
  <div>
    {'msProducts' | snippet : [
        'parents' => $rules['parents'],
        'optionFilters' => $rules['options'],
        'tpl' => '@FILE chunks/product/listing-products-item-slide.tpl',
        'tplWrapper' => '@INLINE {$output}',
        'includeTVs' => 'isFractional,productNotAvailable,freeShipping',
        'includeThumbs' => 'webp',
    ]}
  </div>
{/if}
```

---

## 🛠️ Разработка

### Фронтенд админка (`app/`)

```bash
cd app/
npm install         # Установка зависимостей
npm run dev         # Запуск dev-сервера (Vite)
npm run build       # Сборка production-версии в ../templates/
```

### Бэкенд

- `action.php` Для выполнения запросов в базу установи
  `const PRODUCTION = false;`

  **⚠️ НЕ ЗАБУДЬ ВЕРНУТЬ ОБРАТНО НА TRUE ПРИ ДЕПЛОЕ!**

---
