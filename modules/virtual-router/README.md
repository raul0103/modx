# 🌐 Virtual Router для MODX

Модуль для работы с виртуальными поддоменами без использования `ContextRouter`

## 📁 Структура проекта

- Данные

  - `virtual-router/data/changes/<CONTEXT_KEY>.php` - Данные для подмены на сайте
  - `virtual-router/data/global/<CONTEXT_KEY>/<SUBDOMAIN>.json` - Глобальные данные которые запишутся в плейсхолдер по поддоменам. + `_default.json` - данные по умолчанию

- `virtual-router/plugins` - Плагины. Пока один основной `VirtualRouter.php`
- `virtual-router/index.php` - Основная логика модуля. Не трогать. Подтягивается в плагине `plugins/VirtualRouter.php`

## 🚀 Установка

- Создать плагин в админке `virtual-router/plugins/VirtualRouter.php`
- Добавить данные аналогично тестовым
  - `virtual-router/data/changes/<CONTEXT_KEY>.php`
  - `virtual-router/data/global/<CONTEXT_KEY>/<SUBDOMAIN>.json`
