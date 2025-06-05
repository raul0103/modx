import {createContext, useContext, useState} from "react";
import {ButtonIcon} from "../ui/common/buttons";

const ModalContext = createContext();

export function useModal() {
  return useContext(ModalContext);
}

export function ModalProvider({children}) {
  const [modalTitle, setModalTitle] = useState(null);
  const [modalContent, setModalContent] = useState(null);

  const openModal = (title, component) => {
    setModalTitle(title);
    setModalContent(component);
  };

  const closeModal = () => {
    setModalContent(null);
  };

  return (
    <ModalContext.Provider value={{openModal, closeModal}}>
      {children}
      {modalContent && (
        <div
          className="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 modal flex"
          onClick={closeModal}
        >
          <div
            className="bg-white rounded-lg shadow-xl p-6 w-full max-w-md"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-lg font-medium text-gray-900">
                {modalTitle}
              </h3>
              <ButtonIcon color="gray" icon="times" onClick={closeModal} />
            </div>
            {modalContent}
          </div>
        </div>
      )}
    </ModalContext.Provider>
  );
}
