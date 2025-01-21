let helpers = {
  getDomain() {
    let current_url = new URL(location.href);

    let hostname_split = current_url.hostname.split(".");
    let hostname;
    if (hostname_split.length > 2) {
      hostname_split.shift();
      hostname = hostname_split.join(".");
    } else {
      hostname = current_url.hostname;
    }

    return hostname;
  },

  /**
   * Получает список городов из HTML списка указанного в modules\city-changer\chunks\modals.tpl
   * @param {*} modals - HTML модальных окон полученных по ajax
   * @return {array} result - Массив поддоменов и названий городов
   */
  getCityList(modals) {
    let city_elements = modals.querySelectorAll("[data-city-name]");
    let result = [];
    city_elements.forEach((city_element) => {
      result.push({
        city_subdomain: city_element.dataset.citySubdomain,
        city_name: city_element.dataset.cityName,
      });
    });

    return result;
  },
};

export default helpers;
