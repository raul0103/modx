import { createContext, useState, useEffect } from "react";
import { useSearchParams } from "react-router-dom";

import MainPage from "../pages/main";
import ControlRule from "../pages/rule/control";
import ListRules from "../pages/rule/list";

export const RouterContext = createContext(null);

export function RouterProvider({ children }) {
  const [activePage, setActivePage] = useState("MainPage");
  const [searchParams, setSearchParams] = useSearchParams();

  const handleActivePage = (newActivePage, rest = {}) => {
    // Загнали в новые парамтры старые, что-бы не потерять активную страницу аминки modx
    const url = new URL(location.href);
    const old_params = new URLSearchParams(url.search);
    rest.a = old_params.get("a");
    rest.namespace = old_params.get("namespace");

    const params = new URLSearchParams(rest);
    params.set("page", newActivePage);

    setSearchParams(params);
    setActivePage(newActivePage);
  };

  useEffect(() => {
    const pageFromUrl = searchParams.get("page");
    if (pageFromUrl && pageFromUrl !== activePage) {
      setActivePage(pageFromUrl);
    }
  }, [searchParams]);

  const pages = {
    MainPage: {
      title: "Главная",
      component: <MainPage />,
    },
    ListRules: {
      title: "Список правил",
      component: <ListRules />,
    },
    ControlRule: {
      title: "Редактировать правила",
      component: <ControlRule />,
      hidemenu: true,
    },
  };

  return (
    <RouterContext.Provider value={{ activePage, pages, handleActivePage }}>
      {children}
    </RouterContext.Provider>
  );
}
