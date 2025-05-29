import { useContext } from "react";
import { RouterContext } from "../../providers/router";

export default function CategoriesTable(props) {
  const ctx = useContext(RouterContext);

  return (
    <>
      <div style={{ maxHeight: "500px", overflowX: "auto" }} className="w-full">
        <table className="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
          <thead className="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
              <th className="px-6 py-3 text-center">ID</th>
              <th className="px-6 py-3 text-center">Название</th>
              <th className="px-6 py-3 text-center">Кол-во товаров</th>
              <th className="px-6 py-3 text-center">Действия</th>
            </tr>
          </thead>
          <tbody>
            {props.items?.map((item) => {
              return (
                <tr key={item.id} className="border-b">
                  <th className="px-6 py-4">{item.id}</th>
                  <th className="px-6 py-4">{item.pagetitle}</th>
                  <th className="px-6 py-4 text-center">
                    {item.children_count}
                  </th>
                  <th className="px-6 py-4 text-center">
                    <div className="flex justify-center">
                      {item.with_rule === "1" ? (
                        <button
                          onClick={() =>
                            ctx.handleActivePage("ControlRule", {
                              categoryId: item.id,
                              isEdit: true,
                            })
                          }
                          className="cursor-pointer text-white bg-gradient-to-r from-pink-400 via-pink-500 to-pink-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-pink-300 dark:focus:ring-pink-800 shadow-lg shadow-pink-500/50 dark:shadow-lg dark:shadow-pink-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
                        >
                          ✎
                        </button>
                      ) : (
                        <button
                          onClick={() =>
                            ctx.handleActivePage("ControlRule", {
                              categoryId: item.id,
                            })
                          }
                          className="cursor-pointer text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
                        >
                          +
                        </button>
                      )}
                    </div>
                  </th>
                </tr>
              );
            })}
          </tbody>
        </table>
      </div>
    </>
  );
}
