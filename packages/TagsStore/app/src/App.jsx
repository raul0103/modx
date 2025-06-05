import {useContext} from "react";
import {RouterContext, RouterProvider} from "./providers/router";
import {ModalProvider} from "./providers/modal";
import {DataProvider} from "./providers/data";

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
    <>
      <DataProvider>
        <RouterProvider>
          <ModalProvider>
            <AppContent />
          </ModalProvider>
        </RouterProvider>
      </DataProvider>
    </>
  );
}
