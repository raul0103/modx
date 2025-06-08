import {useState} from "react";
import TypeSelect from "../../../ui/form/type-select";
import Input from "../../../ui/form/input";
import {ButtonGray, ButtonPrimary} from "../../../ui/common/buttons";

export default function CreateTagFormResource({
  closeModal,
  tagType,
  setTagType,
  handlerActionTag,
  category_id,
  mode,
  tag,
}) {
  const [errors, setErrors] = useState({});
  const [formFields, setFormFields] = useState({
    type: tagType,
    tag_id: mode === "update" ? tag.id : "",
    title: mode === "update" ? tag.title : "",
    group_name: mode === "update" ? tag.group_name : "",
    resource_id: mode === "update" ? tag.resource_id : null,
    category_id: mode === "update" ? tag.category_id : category_id,
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    const find_errors = {};

    if (!formFields.resource_id || formFields.resource_id <= 0) {
      find_errors.resource_id = "Заполните поле корректно";
    }

    if (Object.keys(find_errors).length > 0) {
      setErrors(find_errors);
      return;
    }

    setErrors({});

    handlerActionTag(formFields);
  };

  const handleSetFormFields = (e, field, value = null) => {
    setFormFields({
      ...formFields,
      [field]: value ?? e.target.value,
    });
  };

  return (
    <form onSubmit={handleSubmit}>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <TypeSelect onChange={setTagType} tagType={tagType} />

        <Input
          label="Группа"
          value={formFields.group_name}
          onChange={(e) => handleSetFormFields(e, "group_name")}
        />
      </div>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <Input
          label="Название"
          value={formFields.title}
          onChange={(e) => handleSetFormFields(e, "title")}
          error={errors.title}
        />
        <Input
          label="ID ресурса"
          type="number"
          value={formFields.resource_id ?? ""}
          onChange={(e) =>
            handleSetFormFields(e, "resource_id", Number(e.target.value))
          }
          error={errors.resource_id}
        />
      </div>

      <div className="flex justify-end">
        <ButtonGray type="button" onClick={closeModal}>
          Отмена
        </ButtonGray>
        <ButtonPrimary type="submit">Сохранить</ButtonPrimary>
      </div>
    </form>
  );
}
