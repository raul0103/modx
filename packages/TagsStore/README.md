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
    └── action.php      # API-обработчик для фронта
```

---

## 🚀 Production

1. Для деплоя залейте каталог в `{core_path}components/TagsStore/`
2. Создайте записи в "Пространство имен"

```code
{core_path}components/TagsStore/
{assets_path}components/TagsStore/
```

3. Проверить что в action.php `const PRODUCTION = true;`
4. Пробежать по файлам и заменить TagsStore на название своего пакета
5. С app/ запустить `npm run build`
6. Запустить миграции `database/migrations/table_name.php` (Пока через консоль. Не придумал как упростить установку)

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
