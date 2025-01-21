/**
 * Api для определения города пользователя
 */
import Storage from "./Storage";

class Api {
  constructor() {
    this.api = "https://www.travelpayouts.com/whereami?locale=ru";
    this.storage_key = "user-city";
    this.storage = new Storage(this.storage_key);
  }

  async getUserCity() {
    let city_name = this.storage.get(this.storage_key);
    if (city_name) return city_name;

    await fetch(this.api)
      .then((response) => response.json())
      .then((data) => {
        city_name = this.replaceCity(data);
        this.storage.set(city_name);
      })
      .catch((error) => {
        console.error("Ошибка при определении города:", error);
      });

    return city_name;
  }

  /**
   * Функция подмены нежелательных городов
   * @param {object} api_response - Ответ от Api
   */
  replaceCity(api_response) {
    let allowed_countries = ["Россия"];
    let default_city = "Санкт-Петербург";
    if (allowed_countries.indexOf(api_response.country_name) == -1) {
      return default_city;
    } else {
      return api_response.name;
    }
  }
}

export default Api;
