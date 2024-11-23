class SimpleFilter {
  CONNECTOR_URL = "assets/connectors/simple-filter.php";

  constructor() {
    document.querySelectorAll('[name="filter_uniqueid"]').forEach((elem) => {
      this.activatedFilters(elem);
      this.generatePagination(elem.value);
    });
  }

  events = {
    reset: (filter_uniqueid) => {
      const new_url = `${window.location.pathname}`;
      history.replaceState(null, "", new_url);

      let filter_uniqueid_elem = document.querySelector(
        `[name="filter_uniqueid"][value="${filter_uniqueid}"]`
      );
      if (filter_uniqueid_elem) {
        filter_uniqueid_elem.form
          .querySelectorAll('input[type="checkbox"]')
          .forEach((checkbox) => {
            checkbox.checked = false;
          });
      }

      this.ajax(filter_uniqueid);
    },
    change: (target, key, value) => {
      const filter_uniqueid = target.form.filter_uniqueid?.value;

      if (!filter_uniqueid) {
        console.error('Не найдено поле name="filter_uniqueid"');
        return;
      }

      // Получаем текущие GET-параметры
      const url_params = new URLSearchParams(window.location.search);

      // Проверяем наличие нужного параметра
      let filterData = url_params.has(filter_uniqueid)
        ? JSON.parse(url_params.get(filter_uniqueid))
        : { filters: {}, page: 1 };

      filterData.page = 1;

      if (!filterData.filters) {
        filterData.filters = {};
      }

      // Обновляем фильтры
      if (!filterData.filters[key]) {
        filterData.filters[key] = [];
      }

      if (target.checked) {
        // Добавляем значение, если оно отсутствует
        if (!filterData.filters[key].includes(value)) {
          filterData.filters[key].push(value);
        }
      } else {
        // Удаляем значение
        filterData.filters[key] = filterData.filters[key].filter(
          (filter) => filter != value
        );

        // Если фильтр пуст, удаляем ключ
        if (filterData.filters[key].length === 0) {
          delete filterData.filters[key];
        }
      }

      // Если нет активных фильтров, удаляем весь объект
      if (Object.keys(filterData.filters).length === 0) {
        url_params.delete(filter_uniqueid);
      } else {
        // Обновляем параметр
        url_params.set(filter_uniqueid, JSON.stringify(filterData));
      }

      // Обновляем URL без перезагрузки страницы
      const new_url = `${window.location.pathname}?${url_params.toString()}`;
      history.replaceState(null, "", new_url);

      this.ajax(filter_uniqueid);
    },
    paginate: (page, filter_uniqueid) => {
      const url_params = new URLSearchParams(window.location.search);

      let data;
      if (url_params.has(filter_uniqueid)) {
        data = JSON.parse(url_params.get(filter_uniqueid));
      } else {
        data = {};
      }

      data.page = page;

      url_params.set(filter_uniqueid, JSON.stringify(data));

      // Обновляем URL без перезагрузки страницы
      const new_url = `${window.location.pathname}?${url_params.toString()}`;
      history.replaceState(null, "", new_url);

      this.ajax(filter_uniqueid);
    },
  };

  activatedFilters = (elem) => {
    const url_params = new URLSearchParams(window.location.search);

    if (url_params.has(elem.value)) {
      let data = JSON.parse(url_params.get(elem.value));
      if (!data.filters) return;

      Object.keys(data.filters).forEach((option_key) => {
        if (!data.filters[option_key]) return;
        data.filters[option_key].forEach((option_value) => {
          let option_elem = elem.form.querySelector(
            '[name="' + option_key + '"][value="' + option_value + '"]'
          );
          if (option_elem) option_elem.checked = true;
        });
      });
    }
  };

  ajax = (filter_uniqueid) => {
    const url_params = new URLSearchParams(window.location.search);

    const data = {
      filter_uniqueid,
      get_params: Object.fromEntries(url_params.entries()),
    };

    // Отправляем POST-запрос
    fetch(this.CONNECTOR_URL, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => {
        return response.text();
      })
      .then((result) => {
        let products_block = document.getElementById(filter_uniqueid);
        if (products_block) {
          const parser = new DOMParser();
          const doc = parser.parseFromString(result, "text/html");

          products_block.innerHTML = doc.body.innerHTML;
        }

        this.generatePagination(filter_uniqueid);
      })
      .catch((error) => {
        console.error("Ошибка:", error);
      });
  };

  generatePagination(filter_uniqueid) {
    const max_visible = 5; // Максимальное число видимых страниц

    const container = document.getElementById(`pagen-${filter_uniqueid}`);
    if (!container) return;

    container.innerHTML = ""; // Очистить контейнер перед добавлением

    // >>> Получение текущей страницы
    const url_params = new URLSearchParams(window.location.search);
    let data = url_params.has(filter_uniqueid)
      ? JSON.parse(url_params.get(filter_uniqueid))
      : {};
    let current_page = parseInt(data.page || 1, 10);
    // <<<

    // >>> Общее количество страниц
    let total_pages = parseInt(container.dataset.sfTotalPages, 10);
    if (!total_pages || total_pages < 1) return;
    // <<<

    const createPageItem = (page, isActive = false) => {
      const li = document.createElement("li");
      li.textContent = page;
      li.classList.toggle("active", isActive);

      li.addEventListener("click", () => {
        if (!isActive) this.events.paginate(page, filter_uniqueid);
      });
      return li;
    };

    // >>> Диапазон видимых страниц
    const visibleStart = Math.max(
      1,
      current_page - Math.floor(max_visible / 2)
    );
    const visibleEnd = Math.min(total_pages, visibleStart + max_visible - 1);

    // Если есть "Первая страница" вне диапазона
    if (visibleStart > 1) {
      container.appendChild(createPageItem(1)); // Первая страница
      if (visibleStart > 2) {
        const dots = document.createElement("li");
        dots.textContent = "...";
        dots.classList.add("dots");
        container.appendChild(dots); // "..."
      }
    }

    // Основные видимые страницы
    for (let i = visibleStart; i <= visibleEnd; i++) {
      container.appendChild(createPageItem(i, i === current_page));
    }

    // Если есть "Последняя страница" вне диапазона
    if (visibleEnd < total_pages) {
      if (visibleEnd < total_pages - 1) {
        const dots = document.createElement("li");
        dots.textContent = "...";
        dots.classList.add("dots");
        container.appendChild(dots); // "..."
      }
      container.appendChild(createPageItem(total_pages)); // Последняя страница
    }
  }
}

try {
  window.sf = new SimpleFilter();
} catch (e) {
  console.error(`Ошибка модуля SimpleFilter: ${e}`);
}
