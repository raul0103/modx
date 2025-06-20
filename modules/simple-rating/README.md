- Ставим рейтинг в любую форму
- Для получения значения просто в отправленнй форме получаем .active - активные звезды

```js
let form = event.target;

const rating_stars = form.querySelectorAll("[data-rating-star].active");
let rating_value = rating_stars.length;
```
