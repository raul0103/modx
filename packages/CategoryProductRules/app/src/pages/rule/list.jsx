import { useState, useContext } from "react";
import { fetchRules } from "../../lib/data";
import { CTXsContext } from "../../providers/context";

import ContextsSelect from "../../ui/form/contexts-select";
import CategoriesTable from "../../ui/main/categories-table";
import Loader from "../../ui/other/loader";
import Button from "../../ui/common/button";
import PageTitle from "../../ui/common/page-title";

export default function Page() {
  const ctx = useContext(CTXsContext);

  const [categories, setCategories] = useState([]);
  const [errors, setErrors] = useState({});
  const [isLoading, setIsLoading] = useState(false);

  const handleClick = async () => {
    setIsLoading(true);
    setCategories([]);

    await fetchRules({
      contextKey: ctx.activeContext,
      setCategories,
      setErrors,
    });

    setIsLoading(false);
  };

  return (
    <div>
      <PageTitle>Список правил</PageTitle>

      <div className="mb-4">
        {/* Вводные поля и кнопка */}
        <div className="mb-2 font-medium">1. Выбор контекста</div>
        <div className="mb-4 ml-4">
          <ContextsSelect error={errors.activeContext} />
        </div>

        <Button onClick={handleClick}>Запуск</Button>
      </div>

      {/* Результаты */}
      <div className="mb-4 min-h-[100px] flex items-center justify-center">
        {isLoading ? (
          <Loader />
        ) : errors.global ? (
          <div className="text-red-500 text-sm">{errors.global}</div>
        ) : categories.length ? (
          <CategoriesTable items={categories} />
        ) : (
          <div className="text-lg text-gray-400">😴 Жду результат...</div>
        )}
      </div>
    </div>
  );
}
