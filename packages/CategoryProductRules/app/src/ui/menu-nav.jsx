import { useContext } from "react";
import { RouterContext } from "../providers/router";

export default function MenuNav() {
  const ctx = useContext(RouterContext);

  // Без component. Думаю лучше передавать те элементы которые необходимы
  const items = Object.entries(ctx.pages)
    .filter(([_, value]) => !value.hidemenu)
    .map(([key, value]) => {
      const { component, ...rest } = value;
      return { key, ...rest };
    });

  return (
    <nav className="bg-gray-300">
      <div className="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
        <div className="relative flex h-16 items-center justify-between">
          <div className="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
            <div className="hidden sm:ml-6 sm:block">
              <div className="flex space-x-4">
                {items.map((item) => (
                  <button
                    key={item.key}
                    onClick={() => ctx.handleActivePage(item.key)}
                    className={`rounded-md px-3 py-2 text-sm font-medium ${
                      item.key === ctx.activePage
                        ? "bg-gray-900 text-white"
                        : "text-gray-900 hover:bg-gray-700 hover:text-white"
                    }`}
                  >
                    {item.title}
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>
  );
}
