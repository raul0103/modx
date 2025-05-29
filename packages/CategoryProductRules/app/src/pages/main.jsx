import { useState, useContext } from "react";
import { fetchCategories } from "../lib/data";
import { CTXsContext } from "../providers/context";

import Input from "../ui/form/input";
import ContextsSelect from "../ui/form/contexts-select";
import CategoriesTable from "../ui/main/categories-table";
import Loader from "../ui/other/loader";
import Button from "../ui/common/button";
import PageTitle from "../ui/common/page-title";

export default function MainPage() {
  const ctx = useContext(CTXsContext);

  const [categories, setCategories] = useState([]);
  const [productsCount, setProductsCount] = useState(0);
  const [categoryId, setCategoryId] = useState(null);
  const [errors, setErrors] = useState({});
  const [isLoading, setIsLoading] = useState(false);

  const handleClick = async () => {
    setIsLoading(true);
    setErrors({});
    setCategories([]);

    await fetchCategories({
      contextKey: ctx.activeContext,
      productsCount: Number(productsCount),
      categoryId: categoryId ? Number(categoryId) : null,
      setCategories,
      setErrors,
    });

    setIsLoading(false);
  };

  return (
    <div>
      <PageTitle>Главная</PageTitle>

      <div className="mb-4">
        {/* Вводные поля и кнопка */}
        <div className="mb-2 font-medium">1. Выбор контекста</div>
        <div className="mb-4 ml-4">
          <ContextsSelect error={errors.activeContext} />
        </div>

        <div className="mb-2 font-medium">
          2. Ниже можно заполнить одно из полей
        </div>
        <div className="ml-4">
          <div className="mb-2">
            <Input
              label="Минимум товаров в категории"
              type="number"
              error={errors.productsCount}
              onChange={(e) => setProductsCount(e.target.value)}
            />
          </div>
          <div className="mb-4">
            <Input
              label="Поиск по ID категории"
              type="number"
              error={errors.categoryId}
              onChange={(e) => setCategoryId(e.target.value)}
            />
          </div>
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
