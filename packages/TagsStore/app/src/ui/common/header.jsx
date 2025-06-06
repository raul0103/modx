import HelpModal from "../../components/help-modal/wrapper";
import {ButtonIcon} from "./buttons";

export default function Header() {
  return (
    <header className="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
      <div className="container mx-auto px-4 py-6">
        <div className="flex justify-between items-center">
          <h1 className="text-2xl font-bold">Управление тегами</h1>

          <HelpModal />

          {/* <div className="relative">
            <input
              type="text"
              id="search"
              placeholder="Поиск..."
              className="px-4 py-2 rounded-lg text-gray-800 w-64 focus:outline-none focus:ring-2 focus:ring-blue-300"
            />
            <ButtonIcon
              color="gray"
              icon="search"
              className="absolute right-3 top-2 text-gray-500"
            />
          </div> */}
        </div>
      </div>
    </header>
  );
}
