import { useContext } from "react";
import MenuNav from "./ui/menu-nav";
import { ContextProvider } from "./providers/context";
import { RouterContext, RouterProvider } from "./providers/router";

function AppContent() {
  const ctx = useContext(RouterContext);
  return (
    <div className="container mx-auto p-8">
      {ctx.pages[ctx.activePage]["component"]}
    </div>
  );
}

export default function App() {
  return (
    <RouterProvider>
      <ContextProvider>
        <MenuNav />
        <AppContent />
      </ContextProvider>
    </RouterProvider>
  );
}
