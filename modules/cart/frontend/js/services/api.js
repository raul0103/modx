/**
 * Отправка запросов на бэк
 */
import helpers from "../utils/helpers";

const api = {
  response: async (processor, product_data) => {
    let valid_params = helpers.checkParams("api.responses", {
      processor,
      product_data,
    });
    if (!valid_params) return;

    const url = "/";
    try {
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "cart-connect",
          processor,
          product_data,
        }),
      });
      if (!response.ok) {
        throw new Error(`Response status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error(error.message);
    }
  },
};

export default api;
