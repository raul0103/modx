# Компонент: TagsStore

## 📁 Структура проекта

```
components/
└── TagsStore/
    ├── app/            # Исходники фронтенда (React + Vite)
    ├── controllers/    # Инициализация страницы в админке MODX
    ├── actions/        # Экшены для работы с данными в базе
        └── mgr         # Для доступа к mgr контексту проверяется авторизация
        └── web
    ├── templates/      # Сборка фронта (index.html, assets)
    ├── elements/       # Элементы для работы с плагином на сайте (чанки, сниппеты и тд)
    └── action.php      # API-обработчик для фронта
```

---

## 🚀 Production

1. Для деплоя залейте каталог в `{core_path}components/TagsStore/`
2. Создайте записи в "Пространство имен"

```code
Название
TagsStore

Путь к ядру
{core_path}components/TagsStore/

Путь к активам
{assets_path}components/TagsStore/
```

3. Проверить что в action.php `const PRODUCTION = true;`
4. С app/ запустить `npm run build` - Все необходимые файлы попадут в assets/components/TagsStore
5. Запустить миграции `database/migrations/table_name.php` (Пока через консоль. Не придумал как упростить установку)

Если будут ошибки проверь app/.env.production

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

## Настройка на сайте

1. Установить пространство имен
2. Добавить кнопку для перехода в плагин
3. Добавить для ресурсов TV поля куда необходимо вносить ID категорий тегов из плагина
   - TV полей может быть несколько если необходимо выводить теги в разных частях страницы и под разными названиями
4. Вызвать на странице

```php
{set $tags = "@FILE elements/snippets/getTags.php" | snippet : [
  'tvName' => 'НАЗВАНИЕ_ТВ_ПОЛЯ_ГДЕ_ЧЕРЕЗ_ЗАПЯТУЮ_ПЕРЕЧИСЛЕНЫ_ID_КАТЕГОРИЙ_ТЕГОВ'
  'resourceImageTVName' => 'НАЗВАНИЕ_ТВ_ПОЛЯ_КАРТИНКА_КАТЕГОРИИ (используется у ресурсов с type = resource)'
]}

// $tags будет выглядеть следующим образом
[
  {
    "group_name": "Для внутренних перегородок и стен",
    "items": [
      {
        "title": "Роквул Лайт Баттс",
        "uri": "/bats",
        "image": "/assets/img/image.jpg"
      }
    ]
  },
  {
    "group_name": null,
    "items": [
      {
        "title": "Роквул Лайт",
        "uri": "/lite",
        "image": "/assets/img/image.jpg"
      }
    ]
  }
]
```
