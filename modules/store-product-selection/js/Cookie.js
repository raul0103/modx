class Cookie {
  /**
   * Получает значение куки по ключу
   * @param {string} key - Ключ куки
   * @returns {string|null} - Значение куки или null, если не найдено
   */
  get(key) {
    let name = key + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i].trim();
      if (c.indexOf(name) === 0) {
        return c.substring(name.length, c.length);
      }
    }
    return null;
  }

  /**
   * Устанавливает куки
   * @param {string} key - Ключ куки
   * @param {*} value - Значение куки
   * @param {string} [domain] - Домен для видимости куки на поддоменах (опционально)
   */
  set(key, value = true, domain) {
    document.cookie =
      key + "=" + value + "; path=/" + (domain ? `; domain=.${domain}` : "");
  }

  /**
   * Удаляет куки
   * @param {string} key - Ключ куки
   */
  delete(key) {
    this.set(key, "", null, -1); // Устанавливаем отрицательное количество дней для удаления куки
  }
}

export default Cookie;
