import {useState} from "react";

import CreateTagFormResource from "./resource";
import CreateTagFormCustom from "./custom";

import {createTagAction, updateTagAction} from "../../../lib/actions";
import {fetchTagsByCategoryId} from "../../../lib/fetch";
import {useModal} from "../../../providers/modal";

export default function TagCreateForm({
  category_id,
  setTags,
  mode = "create",
  tag = {},
}) {
  const {closeModal} = useModal();
  const [tagType, setTagType] = useState(
    mode === "update" ? tag.type : "resource"
  );

  const handlerActionTag = async (formFields) => {
    const data =
      mode === "update"
        ? await updateTagAction(formFields)
        : await createTagAction(formFields);
    if (data) {
      await fetchTagsByCategoryId(formFields.category_id).then((data) => {
        if (data && data.length) setTags(data);
      });

      closeModal();
    }
  };

  if (tagType === "resource") {
    return (
      <CreateTagFormResource
        mode={mode}
        tag={tag}
        closeModal={closeModal}
        tagType={tagType}
        setTagType={setTagType}
        handlerActionTag={handlerActionTag}
        category_id={category_id}
      />
    );
  }

  if (tagType === "custom") {
    return (
      <CreateTagFormCustom
        mode={mode}
        tag={tag}
        closeModal={closeModal}
        tagType={tagType}
        setTagType={setTagType}
        handlerActionTag={handlerActionTag}
        category_id={category_id}
      />
    );
  }
}
