import {removeTagAction} from "../../../lib/actions";
import {useModal} from "../../../providers/modal";
import {ButtonIcon} from "../../../ui/common/buttons";
import TagCreateForm from "../create-form/wrapper";

export default function TagItem({item, handlerFetchTags, setTags}) {
  const {openModal} = useModal();

  const handleUpdateTag = () => {
    openModal(
      "Редактировать тег",
      <TagCreateForm mode="update" tag={item} setTags={setTags} />
    );
  };

  const handlerRemoveTag = async () => {
    if (!confirm("Удалить тег?")) return;

    const response = await removeTagAction({tag_id: item.id});
    if (response) {
      alert("Тег удален");
      await handlerFetchTags();
    }
  };

  return (
    <>
      <tr key={item.id} className="border-b border-gray-200 hover:bg-gray-50">
        <td className="py-3 px-4">{item.title}</td>
        <td className="py-3 px-4">{item.uri}</td>
        <td className="py-3 px-4">
          {item.image && <img src={item.image} width={40} height={40} />}
        </td>
        <td className="py-3 px-4">{item.type}</td>
        <td className="py-3 px-4">{item.resource_id}</td>
        <td className="py-3 px-4">{item.group_name}</td>
        <td className="py-3 px-4">
          <ButtonIcon
            icon="edit"
            color="blue"
            tooltip="Редактировать тег"
            onClick={handleUpdateTag}
          />
          <ButtonIcon
            icon="trash"
            color="red"
            tooltip="Удалить тег"
            onClick={handlerRemoveTag}
          />
        </td>
      </tr>
    </>
  );
}
