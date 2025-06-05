import actionApi from "./api";

export const fetchContextCategories = async () => {
  const response = await actionApi.handle("fetch.context-categories");

  if (response.success == true) {
    return response.data;
  } else {
    console.error("Ошибка при получении данных");
    return false;
  }
};

export const fetchTagsByCategoryId = async (category_id) => {
  const response = await actionApi.handle("fetch.category-tags", {category_id});

  if (response.success == true) {
    return response.data;
  } else {
    console.error("Ошибка при получении данных");
    return false;
  }
};
