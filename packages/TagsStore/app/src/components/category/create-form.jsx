import {useState} from "react";

import {useData} from "../../providers/data";
import {createCategoryAction, updateCategoryAction} from "../../lib/actions";

import Input from "../../ui/form/input";
import {ButtonGray, ButtonPrimary} from "../../ui/common/buttons";
import {useModal} from "../../providers/modal";

const action = {
  create: async (formFields, addCategory) => {
    const new_category = await createCategoryAction(formFields);
    if (new_category) {
      addCategory(formFields.context_key, new_category);
    }
  },
  update: async (formFields, updateCategory) => {
    const response = await updateCategoryAction(formFields);
    if (response) {
      updateCategory(formFields.context_key, response);
    }
  },
};

/**
 *
 * @param {*} context_key
 * @param {*} category - если передана категория - значит это форма редактирования
 * @returns
 */
export default function CategoryCreateForm({
  context_key,
  mode = "create",
  category = {},
}) {
  const {closeModal} = useModal();
  const {addCategory, updateCategory} = useData();
  const [errors, setErrors] = useState({});
  const [formFields, setFormFields] = useState({
    category_id: mode === "update" ? category.id : null,
    name: mode === "update" ? category.name : "",
    context_key: mode === "update" ? category.context_key : context_key,
  });

  const handleSubmit = async (e) => {
    e.preventDefault();

    const find_errors = {};

    if (!formFields.name) find_errors.name = "Укажите название категории";

    if (Object.keys(find_errors).length > 0) {
      setErrors(find_errors);
      return;
    }

    setErrors({});

    if (mode === "update") {
      action.update(formFields, updateCategory);
    } else {
      action.create(formFields, addCategory);
    }

    closeModal();
  };

  return (
    <>
      <form onSubmit={handleSubmit}>
        <div className="mb-4">
          <Input
            label="Название категории"
            value={formFields.name}
            onChange={(e) =>
              setFormFields({...formFields, name: e.target.value})
            }
            error={errors.name}
          />
        </div>
        <div className="mb-4">
          <label
            htmlFor="categoryContext"
            className="block text-sm font-medium text-gray-700 mb-1"
          >
            Контекст - {context_key}
          </label>
        </div>
        <div className="flex justify-end">
          <ButtonGray type="button" onClick={closeModal}>
            Отмена
          </ButtonGray>
          <ButtonPrimary>Сохранить</ButtonPrimary>
        </div>
      </form>
    </>
  );
}
