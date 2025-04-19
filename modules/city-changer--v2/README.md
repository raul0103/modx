- Перенести json/cities.json в доступное для фронта место. По умолчанию assets/template/json/city-changer.json
- Подключить JS

```js
try {
  window.city_changer = new CityChanger();
  window.city_changer.init();
} catch (e) {
  console.error(`Ошибка в классе CityChanger: ${e}`);
}
```

- Подключить SCSS
- На кнопку которая будет открывать модалку с городами повесить onclick="city_changer.activate()"
- На место где будет отображаться выбранный город data-city-changer-select-value
- Вывести в удобном месте (обычно в модалке) чанк chunks/wrapper.tpl
