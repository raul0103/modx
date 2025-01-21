class Storage {
  constructor(key = null) {
    this.key = key ?? "city-changer";
  }

  get(key) {
    let name = (key ?? this.key) + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(";");
    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == " ") {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
  }

  /**
   *
   * @param {*} value
   * @param {*} domain - Необходимо для видимости на поддоменах
   */
  set(value = true, domain) {
    document.cookie =
      this.key + "=" + value + ";" + (domain ? `domain=.${domain};` : "") + "path=/;";
  }

  delete() {
    document.cookie = this.key + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
  }
}

export default Storage;
