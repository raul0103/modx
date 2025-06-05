import {useState} from "react";

import Input from "../../../ui/form/input";
import {ButtonGray, ButtonPrimary} from "../../../ui/common/buttons";
import TypeSelect from "../../../ui/form/type-select";

export default function CreateTagFormCustom({
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
    uri: mode === "update" ? tag.uri : "",
    image: mode === "update" ? tag.image : "",
    group_name: mode === "update" ? tag.group_name : "",
    category_id: mode === "update" ? tag.category_id : category_id,
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    const find_errors = {};

    if (!formFields.title) find_errors.title = "Заполните название";
    if (!formFields.uri) find_errors.uri = "Заполните ссылку";

    if (Object.keys(find_errors).length > 0) {
      setErrors(find_errors);
      return;
    }

    setErrors({});

    handlerActionTag(formFields);
  };

  const handleSetFormFields = (e, field) => {
    setFormFields({
      ...formFields,
      [field]: e.target.value,
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
          label="Ссылка"
          value={formFields.uri}
          onChange={(e) => handleSetFormFields(e, "uri")}
          error={errors.uri}
        />
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <Input
          label="Картинка"
          value={formFields.image}
          onChange={(e) => handleSetFormFields(e, "image")}
        />
      </div>
      <div className="flex justify-end">
        <ButtonGray type="button" onClick={closeModal}>
          Отмена
        </ButtonGray>
        <ButtonPrimary>Сохранить</ButtonPrimary>
      </div>
    </form>
  );
}
