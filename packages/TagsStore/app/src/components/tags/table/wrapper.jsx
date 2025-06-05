import {useRef} from "react";
import {useEffect} from "react";

import {fetchTagsByCategoryId} from "../../../lib/fetch";
import TagItem from "./item";

const handlerFetchTags = async (category_id, setTags) => {
  await fetchTagsByCategoryId(category_id).then((data) => {
    if (data && data.length) setTags(data);
  });
};

export default function TagsItemTable({isOpen, category_id, tags, setTags}) {
  const hasFetched = useRef(false);

  useEffect(() => {
    if (isOpen && !hasFetched.current) {
      hasFetched.current = true;
      handlerFetchTags(category_id, setTags);
    }
  }, [isOpen, category_id]);

  if (!isOpen || !tags) return null;

  return (
    <>
      <div className={`table-container ${isOpen && "open"}`}>
        <table className="min-w-full bg-white">
          <thead>
            <tr className="bg-gray-100 text-gray-600 text-sm leading-normal">
              <th className="py-3 px-4 text-left">Название</th>
              <th className="py-3 px-4 text-left">Ссылка</th>
              <th className="py-3 px-4 text-left">Картинка</th>
              <th className="py-3 px-4 text-left">Тип</th>
              <th className="py-3 px-4 text-left">ID ресурса</th>
              <th className="py-3 px-4 text-left">Группа</th>
              <th className="py-3 px-4 text-left">Действия</th>
            </tr>
          </thead>
          <tbody className="text-gray-600 text-sm">
            {tags.map((item) => {
              return (
                <TagItem
                  item={item}
                  key={item.id}
                  setTags={setTags}
                  handlerFetchTags={() =>
                    handlerFetchTags(category_id, setTags)
                  }
                />
              );
            })}
          </tbody>
        </table>
      </div>
    </>
  );
}
