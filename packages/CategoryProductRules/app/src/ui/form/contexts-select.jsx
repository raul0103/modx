import { useContext } from "react";
import { CTXsContext } from "../../providers/context";
import FieldError from "../field-error";

export default function ContextsSelect(props) {
  const ctx = useContext(CTXsContext);

  if (!ctx.contexts) return <div>Загрузка...</div>;

  return (
    <>
      <select
        value={ctx.activeContext ?? ""}
        onChange={(e) => ctx.setActiveContext(e.target.value)}
        className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
      >
        <option value="">Выбрать контекст</option>
        {ctx.contexts.map((context) => {
          return (
            <option key={context.key} value={context.key}>
              {context.name}
            </option>
          );
        })}
      </select>
      <FieldError error={props.error} />
    </>
  );
}
