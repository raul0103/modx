import {createContext, useContext, useState} from "react";
import {fetchContextCategories} from "../lib/fetch";
import {useEffect} from "react";

const DataContext = createContext();

export function DataProvider({children}) {
  const [contextCategories, setContextCategories] = useState([]);

  const fetchData = async () => {
    const data = await fetchContextCategories();
    if (data) {
      setContextCategories(data);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const addCategory = (context_key, new_category) => {
    setContextCategories((prev) =>
      prev.map((ctx) =>
        ctx.context_key === context_key
          ? {...ctx, categories: [...ctx.categories, new_category]}
          : ctx
      )
    );
  };

  const updateCategory = (context_key, new_category) => {
    setContextCategories((prev) =>
      prev.map((ctx) =>
        ctx.context_key === context_key
          ? {
              ...ctx,
              categories: ctx.categories.map((category) => {
                if (category.id === new_category.id) {
                  category = new_category;
                }

                return category;
              }),
            }
          : ctx
      )
    );
  };

  const removeCategory = (context_key, category_id) => {
    setContextCategories((prev) =>
      prev.map((ctx) =>
        ctx.context_key === context_key
          ? {
              ...ctx,
              categories: ctx.categories.filter(
                (category) => category.id !== category_id
              ),
            }
          : ctx
      )
    );
  };

  return (
    <DataContext.Provider
      value={{contextCategories, addCategory, updateCategory, removeCategory}}
    >
      {children}
    </DataContext.Provider>
  );
}

export function useData() {
  return useContext(DataContext);
}
