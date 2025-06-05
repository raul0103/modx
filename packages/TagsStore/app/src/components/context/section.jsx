import {ButtonIcon} from "../../ui/common/buttons";
import {useModal} from "../../providers/modal";
import CategorySection from "../category/section";
import CategoryCreateForm from "../category/create-form";

export default function ContextSection({context_key, categories}) {
  const {openModal} = useModal();

  const handleOpen = () => {
    openModal(
      "Создать новую категорию",
      <CategoryCreateForm context_key={context_key} />
    );
  };

  return (
    <>
      <div className="context-section pl-4 pb-2">
        <div className="flex justify-between items-center mb-4">
          <h3 className="text-lg font-medium text-gray-500">{context_key}</h3>
          <ButtonIcon
            color="green"
            icon="plus"
            tooltip="Создать категорию"
            onClick={handleOpen}
          />
        </div>
        {categories.map((category) => {
          return (
            <CategorySection
              key={category.id}
              category={category}
              tags={category.tags}
            />
          );
        })}
      </div>
    </>
  );
}
