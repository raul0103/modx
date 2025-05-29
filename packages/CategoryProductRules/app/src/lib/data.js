import { actionAPI } from "./api";

export const fetchCategories = async ({
  contextKey,
  productsCount,
  categoryId,
  setCategories,
  setErrors,
}) => {
  const errors = {};
  setErrors(errors);

  if (!contextKey) errors.activeContext = "Выберите контекст";
  if (
    (!productsCount || productsCount === 0) &&
    (!categoryId || categoryId === 0)
  ) {
    errors.categoryId = errors.productsCount =
      "Укажите минимум товаров или ID категории";
  }

  if (Object.keys(errors).length > 0) {
    setErrors(errors);
    return;
  }

  const response = await actionAPI.handle("get-categories", {
    context: "mgr",
    products_count: productsCount,
    context_key: contextKey,
    category_id: categoryId,
  });

  if (response.success === true) {
    setCategories(response.data);
  } else {
    setCategories([]);
    setErrors({ global: "Не удалось получить данные" });
  }
};

export const fetchRules = async ({ contextKey, setCategories, setErrors }) => {
  const errors = {};
  setErrors(errors);

  if (!contextKey) errors.activeContext = "Выберите контекст";

  if (Object.keys(errors).length > 0) {
    setErrors(errors);
    return;
  }

  const response = await actionAPI.handle("get-rules", {
    context: "mgr",
    context_key: contextKey,
  });

  if (response.success === true) {
    setCategories(response.data);
  } else {
    setCategories([]);
    setErrors({ global: "Не удалось получить данные" });
  }
};

export const fetchCategory = async (categoryId) => {
  const response = await actionAPI.handle("get-category", {
    context: "mgr",
    category_id: categoryId,
  });

  return response;
};

export const fetchRule = async (categoryId) => {
  const response = await actionAPI.handle("get-rule", {
    context: "mgr",
    category_id: categoryId,
  });

  return response;
};
