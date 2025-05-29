import { actionAPI } from "../lib/api";
import { createContext, useEffect, useState } from "react";

// Создаем сам контекст
export const CTXsContext = createContext(null);

// Провайдер
export function ContextProvider({ children }) {
  const [contexts, setContexts] = useState([]);

  const [activeContext, setActiveContext] = useState(null);

  useEffect(() => {
    async function fetchContexts() {
      try {
        const data = await actionAPI.handle("get-contexts-list", {
          context: "mgr",
        });
        if (data.success === true) {
          setContexts(data.data);
        }
      } catch (error) {
        console.error("Ошибка загрузки контекстов", error);
        setContexts([]);
      }
    }

    fetchContexts();
  }, []);

  return (
    <CTXsContext.Provider value={{ activeContext, setActiveContext, contexts }}>
      {children}
    </CTXsContext.Provider>
  );
}
