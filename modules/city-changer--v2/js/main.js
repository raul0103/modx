export default class CityChanger {
  constructor() {
    this.config = {
      find_cities_limit: 30, // Кол-во найденных городо вчерез поиск
      // default_city: CITY_CHANGE || "Москва",
      storage_key: "city-changer", // Ключ для хранения города в локальном хранилище
    };
    this.data = {
      path: "/assets/template/json/city-changer.json",
      value: null,
    };

    // По ним ведется поиск элементов на странице
    this.selectors = {
      main: "[data-city-changer]",
      districts: "[data-city-changer-districts]",
      regions: "[data-city-changer-regions]",
      cities: "[data-city-changer-cities]",
      search_values: "[data-city-changer-search-values]",
      // select_value: "[data-city-changer-select-value]",
    };
    // Найлденные элементы
    this.elements = {
      main: null,
      districts: null,
      regions: null,
      cities: null,
      search_values: null,
      // select_value: null,
    };

    /**
     * Что будет отображено в элементах
     * При измениии данного объекта запускам this.renderDom()
     */
    this.render_data = {
      districts: null,
      regions: null,
      cities: null,
    };

    // ID выбранных элеменитов
    this.active_elements_id = {
      districts: null,
      regions: null,
      cities: null,
    };
  }

  async init() {
    this.initDOMElements();
    // this.setCity();
  }

  // УСтановит город в элемент this.elements.select_value на странице
  // setCity() {
  //   const elements = document.querySelectorAll(
  //     "[data-city-changer-select-value]"
  //   );
  //   if (!elements.length) return;

  //   let city = localStorage.getItem(this.config.storage_key);
  //   if (!city || this.config.default_city == "Москва")
  //     city = this.config.default_city;

  //   elements.forEach((elem) => {
  //     elem.textContent = city;
  //   });
  // }

  // Активация модуля
  async activate() {
    this.data.value = await this.getJSONData();

    if (!this.data.value) {
      this.log.error("Не удалось получить данные JSON");
    }

    this.generateRenderData("districts", 0);
    this.renderDomByData();
  }

  input(e) {
    let find_cities = {
      cities: [],
    };
    if (e.value && this.data.value)
      this.data.value.cities.forEach((city) => {
        if (find_cities.cities.length > this.config.find_cities_limit) return;
        if (city.name.toLowerCase().indexOf(e.value.toLowerCase()) > -1) {
          find_cities.cities.push(city);
        }
      });

    if (find_cities.cities.length > 0) {
      this.elements.search_values.classList.add("opened");
    } else {
      this.elements.search_values.classList.remove("opened");
    }

    this.renderDomByData(
      [
        {
          key: "cities",
          element: "search_values",
        },
      ],
      find_cities
    );
  }

  /**
   * Перерисовка DOM в соответсвии с переданными данными data
   * @param {*} keys - Можно передать 1 параметр для отрисовки в любой части сайта
   */
  renderDomByData(
    keys = [
      {key: "districts", element: "districts"},
      {key: "regions", element: "regions"},
      {key: "cities", element: "cities"},
    ],
    data = this.render_data
  ) {
    keys.forEach((value) => {
      let render_data = "city_changer.renderDomByData()";
      if (value.key === "cities") render_data = "";
      let html = "";
      data[value.key].forEach((item) => {
        let classes =
          this.active_elements_id[value.key] == item.id ? "active" : "";
        html += `<li>
                      <button
                              class="${classes}" 
                              onclick="city_changer.generateRenderData('${value.key}','${item.id}');${render_data}">
                              ${item.name}
                      </button>
                  </li>`;
      });
      html = `<ul>${html}</ul>`;
      this.elements[value.element].innerHTML = html;
    });
  }

  /**
   * По переданным параметрам сформирует данные необходимые для вывода
   *
   * @param {string} key [districts | regions | cities] - Ключ от которого начнется выборка
   * @param {integer} id - Значение
   */
  async generateRenderData(key, id) {
    // Всегда одни и те же округи
    this.render_data.districts = this.data.value.districts;

    if (key === "districts") {
      this.active_elements_id.districts = id;

      // Нашли регионы округа
      this.render_data.regions = [];
      this.data.value.regions.forEach((region) => {
        if (region.districtId == id) {
          this.render_data.regions.push(region);
        }
      });

      // Нашли города региона
      let first_region_id = this.render_data.regions[0].id;
      this.active_elements_id.regions = first_region_id;

      this.render_data.cities = [];
      this.data.value.cities.forEach((city) => {
        if (first_region_id === city.regionId) {
          this.render_data.cities.push(city);
        }
      });
    } else if (key === "regions") {
      this.active_elements_id.regions = id;

      // Нашли города региона
      this.render_data.cities = [];
      this.data.value.cities.forEach((city) => {
        if (city.regionId == id) {
          this.render_data.cities.push(city);
        }
      });
    } else if (key === "cities") {
      this.active_elements_id.cities = id;

      let find_city = this.data.value.cities.find((city) => city.id === id);
      if (find_city) {
        const [key, name] = [this.config.storage_key, find_city.name];

        // this.setCity();
        localStorage.setItem(key, name);

        await fetch("api/cookie.php", {
          method: "POST",
          body: JSON.stringify({action: "set", name: key, value: name}),
        });
      }

      location.reload();

      // let subdomain = await window.getChangeCitySubdomain(find_city.name);
      // if (!subdomain) {
      //   subdomain = "moskva";
      // }

      // if (subdomain) {
      //   if (subdomain == "moskva" && !CONTEXT_PREFIX[CONTEXT_KEY]) {
      //     location.href = "https://" + MAIN_HOST + location.pathname;
      //   } else {
      //     location.href =
      //       "https://" +
      //       CONTEXT_PREFIX[CONTEXT_KEY] +
      //       subdomain +
      //       "." +
      //       MAIN_HOST +
      //       location.pathname;
      //   }
      // } else location.reload();
    }
  }

  // Поиск элементов на странице
  initDOMElements() {
    // Поиск вложенных элементов
    Object.keys(this.elements).forEach((key) => {
      if (key === "main") return;

      this.elements[key] = this.elements.main?.querySelector(
        this.selectors[key]
      );

      // Пытаемся найти нак же во всем документе
      if (!this.elements[key])
        this.elements[key] = document.querySelector(this.selectors[key]);

      if (!this.elements[key]) {
        this.log.warn(`Не найден элемент: ${key}`);
      }
    });
  }

  async getJSONData() {
    let response = await fetch(this.data.path);

    if (!response.ok) {
      this.log.error("Ошибка HTTP: " + response.status);
      return null;
    }
    return await response.json();
  }

  log = {
    error(message) {
      console.error(`[CityChanger] ${message}`);
    },
    warn(message) {
      console.warn(`[CityChanger] ${message}`);
    },
  };
}
