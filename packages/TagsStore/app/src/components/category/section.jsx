import {useState} from "react";

import {useModal} from "../../providers/modal";
import {useData} from "../../providers/data";

import TagsItemTable from "../tags/table/wrapper";
import TagCreateForm from "../tags/create-form/wrapper";
import {ButtonIcon} from "../../ui/common/buttons";
import {removeCategoryAction} from "../../lib/actions";
import CategoryCreateForm from "./create-form";

export default function CategorySection({category}) {
  const [tags, setTags] = useState(null);

  const [isOpen, setIsOpen] = useState(false);
  const {openModal} = useModal();
  const {removeCategory} = useData();

  const handleOpenCategory = () => {
    openModal(
      "Создать новый тег",
      <TagCreateForm category_id={category.id} setTags={setTags} />
    );
  };

  const handleRemoveCategory = async () => {
    if (confirm("Теги в данной категории так же будут удалены")) {
      const response = await removeCategoryAction({category_id: category.id});
      if (response) {
        alert("Категория удалена");
        removeCategory(category.context_key, category.id);
      }
    }
  };

  const handleUpdateCategory = () => {
    openModal(
      "Редактировать категорию",
      <CategoryCreateForm
        context_key={category.context_key}
        mode="update"
        category={category}
      />
    );
  };

  return (
    <>
      <div className="grid grid-cols-1 lg:grid-cols-1 gap-4 mb-2">
        <div className="category-card bg-white rounded-lg shadow-md">
          <div
            className="p-4 bg-blue-50 cursor-pointer"
            onClick={() => setIsOpen(!isOpen)}
          >
            <div className="flex justify-between items-center">
              <h4 className="font-medium text-gray-800 flex items-center gap-2">
                <span className="text-sm text-gray-600">{category.id}</span>
                {category.name}
              </h4>

              <div onClick={(e) => e.stopPropagation()}>
                <ButtonIcon
                  color="blue"
                  icon="edit"
                  tooltip="Редактировать категорию"
                  onClick={handleUpdateCategory}
                />
                <ButtonIcon
                  color="red"
                  icon="trash"
                  tooltip="Удалить категорию"
                  onClick={handleRemoveCategory}
                />
                <ButtonIcon
                  onClick={handleOpenCategory}
                  color="green"
                  icon="plus"
                  tooltip="Создать тег"
                />
              </div>
            </div>
          </div>
          <TagsItemTable
            isOpen={isOpen}
            category_id={category.id}
            tags={tags}
            setTags={setTags}
          />
        </div>
      </div>
    </>
  );
}
