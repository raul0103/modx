const helpers = {
  /**
   * Проверка переданных параметров в функцию
   * @param {*} function_name - Название функции в которой идет проверка
   * @param {*} param1 - объект параметров {product_id,name, ...}
   * @returns {boolean}
   */
  checkParams: (function_name, { ...param }) => {
    let param_keys = Object.keys(param);
    let valid = true;
    param_keys.forEach((param_key) => {
      if (!param[param_key]) {
        console.warn(`[${function_name}] Не передан параметр ${param_key}`);
        valid = false;
      }
    });

    return valid;
  },
};

export default helpers;
