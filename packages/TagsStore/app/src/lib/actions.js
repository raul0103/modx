import actionApi from "./api";

export const createCategoryAction = async (formFields) => {
  const response = await actionApi.handle("category.create", formFields);

  if (response.success == true) {
    return response.data;
  } else {
    console.error("Ошибка при создании категории");
  }
};

export const updateCategoryAction = async (formFields) => {
  const response = await actionApi.handle("category.update", formFields);

  if (response.success == true) {
    return response.data;
  } else {
    console.error("Ошибка при создании категории");
  }
};

export const removeCategoryAction = async (formFields) => {
  const response = await actionApi.handle("category.remove", formFields);

  if (response.success == true) {
    return true;
  } else {
    console.error("Ошибка при удалении категории");
    return false;
  }
};

export const createTagAction = async (formFields) => {
  const response = await actionApi.handle("tag.create", formFields);

  if (response.success == true) {
    return response.data;
  } else {
    console.error("Ошибка при создании тега");
  }
};

export const updateTagAction = async (formFields) => {
  const response = await actionApi.handle("tag.update", formFields);

  if (response.success == true) {
    return response.data;
  } else {
    console.error("Ошибка при обновлении тега");
  }
};

export const removeTagAction = async (formFields) => {
  const response = await actionApi.handle("tag.remove", formFields);

  if (response.success == true) {
    return true;
  } else {
    console.error("Ошибка при удалении тега");
    return false;
  }
};
