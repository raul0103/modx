// Обновления элементов
const update_elements = {
  // По ID input будут записаны ссылки на элементы, что-бы повторно не бегать по странице и не искать
  find_elements: {
    counters: {},
    product_summs: {},
    total_count: [],
    total_summ: [],
  },

  /**
   * Функция контсруктор для всех остальных
   * @param {*} value - Значение элемента
   * @param {object} finds - Объект в котором обязательно есть {key:"КЛЮЧ К ПЕРЕМЕННОЙ В ОБЪЕКТЕ",child: "ОПЦИОНАЛЬНО для объектов"}
   * @param {*} selector - Селектор для поиска элементов
   * @param {*} warn - Предупреждение
   * @param {*} field - Поле для замены
   * @returns
   */
  main(value, finds, selector, warn, field) {
    if (!value) value = 0;

    let elements = finds.child
      ? this.find_elements[finds.key][finds.child]
      : this.find_elements[finds.key];

    if (!elements || !elements.length) {
      elements = document.querySelectorAll(selector);

      // Обновляем объект find_elements
      if (finds.child) {
        this.find_elements[finds.key][finds.child] = elements;
      } else {
        this.find_elements[finds.key] = elements;
      }
    }

    if (elements.length == 0) {
      console.warn(warn);
      return;
    }

    elements.forEach((element) => {
      element[field] = value;

      // Если true то добавит класс hidden если значение 0
      if (element.dataset.totalHideEmpty == "true") {
        if (value < 1) {
          element.classList.add("hidden");
        } else {
          element.classList.remove("hidden");
        }
      }
    });
  },

  /**
   * Обновит товару его кол-во на странице в input
   * data-cart-product-count="PRODUCT_ID" - Обязательный аттрибут на input кол-ва товара. Будет обновляться по AJAX
   *
   * @param {*} data
   * @returns
   */
  productCount(data) {
    let product_count = data.product_data.count ?? 0;

    let selector = `[data-cart-product-count="${data.product_data.id}"]`;
    this.main(
      product_count,
      {
        key: "counters",
        child: data.product_data.id,
      },
      selector,
      `Не найдены input с кол-вом товаров: ${selector}`,
      "value"
    );
  },

  /**
   * Обновит товару сумму в найденных элементах по аттрибуту
   * data-cart-product-summ="PRODUCT_ID" - Обязательный аттрибут
   *
   * @param {*} data
   * @returns
   */
  productSumm(data) {
    let selector = `[data-cart-product-summ="${data.product_data.id}"]`;
    this.main(
      data.product_data.summ,
      {
        key: "product_summs",
        child: data.product_data.id,
      },
      selector,
      `Не найдены элементы для обновления суммы товара: ${selector}`,
      "textContent"
    );
  },

  /**
   * Обновит на странице общее кол-во товаров на странице у элементов с аттрибутом
   * data-cart-total-count
   *
   * @param {*} data
   */
  cartTotalCount(data) {
    let selector = `[data-cart-total-count]`;
    this.main(
      data.total.count,
      {
        key: "total_count",
      },
      selector,
      `Не найдены элементы для обновления общего кол-ва товара в корзине: ${selector}`,
      "textContent"
    );
  },

  /**
   * Обновит на странице общую сумму товаров в корзине
   * data-cart-total-summ
   *
   * @param {*} data
   */
  cartTotalSumm(data) {
    [
      {
        selector: `[data-cart-total-summ]`,
        value: data.total.summ,
        key: "total_summ",
      },
      {
        selector: `[data-cart-total-summ-old]`,
        value: data.total.old_summ,
        key: "total_old_summ",
      },
    ].forEach((item) => {
      this.main(
        item.value,
        {
          key: item.key,
        },
        item.selector,
        `Не найдены элементы для обновления общей суммы товаров в корзине: ${item.selector}`,
        "textContent"
      );
    });
  },
};

export default update_elements;
