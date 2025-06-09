# 🌐 Virtual Router для MODX

Модуль для работы с виртуальными поддоменами без использования `ContextRouter`

## 🚀 Установка

- Создать плагин в админке `virtual-router/plugins/VirtualRouter.php`
- Добавить данные в data
- У контекста должен быть обязательный параметр http_host

## 📁 Структура проекта

### 📦 Статическая часть

- `components/` — Компоненты, содержащие основную логику модуля
- `index.php` — Точка входа

---

### ✏️ Конфигурация и редактируемые данные

#### 🔧 `config/` _(опционально)_

Файлы конфигураций для контекстов.  
Пример:  
`virtual-router/config/contexts/_example.php`

#### 📂 `data/` _(обязательно)_

Данные для формирования глобального плейсхолдера `virtual-router`.
s

- `data/changes/<CONTEXT_NAME>.php`

  Формирует замену слов/фраз в зависимости от контекста.  
   Пример файла:

  ```php
  <?php

  return function ($global_data) {
      return [
          "Москва"     => $global_data["toponim"]["base"]["standart"],
          "в Москве"   => $global_data["toponim"]["where"]["standart"],
          "по Москве"  => $global_data["toponim"]["on"]["standart"],
          "Москвы"     => $global_data["toponim"]["what"]["standart"],
      ];
  };
  ```

- **`data/global/`** — Хранилище глобальных данных, подставляемых по каскаду:
  `common → context → regions`
  Порядок определяется в файле:
  `virtual-router/components/providers/GlobalProvider.php`

  - **`data/global/common/data.json`** —
    Общий файл, применяемый ко всем контекстам и регионам. Один на всю систему.

  - **`data/global/contexts/<CONTEXT_KEY>.json`** —
    Данные, специфичные для конкретного контекста (например: `web.json`, `mgr.json`).

  - **`data/global/regions/`** —
    Данные для поддоменов (регионов).
    Структура вложенности:
    ```
    data/global/regions/<НАЗВАНИЕ_РЕГИОНА_ИЛИ_ОБЛАСТИ>/<ПОДДОМЕН>.json
    ```
    **Пример:**
    `data/global/regions/msk-oblast/himki.json`

## Пример возвращаемых данных

himki.domain.ru

```json
{
  "toponim": {
    "base": {"standart": "Москва"},
    "where": {"standart": "в Москве"},
    "on": {"standart": "по Москве"},
    "what": {"standart": "Москвы"}
  },
  "address": "Адрес для города himki из файлы data/global/regions/msk-oblast/himki.json",
  "phone": "+7 (777) 777-77-77",
  "region": {
    "toponim": {
      "base": {"standart": "Московская область"},
      "where": {"standart": "в Московской области"},
      "on": {"standart": "по Московской области"},
      "what": {"standart": "Московской области"}
    }
  }
}
```
