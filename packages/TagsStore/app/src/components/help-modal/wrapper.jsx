import {useModal} from "../../providers/modal";

function HelpContent() {
  return (
    <div className="text-sm">
      <p>
        Первое что вас встретит - <b>список контекстов</b>
      </p>
      <p>
        Напротив каждого контекста есть кнопка{" "}
        <span className="text-lg">"+"</span> для создания <b>категории тегов</b>
      </p>
      <div className="mb-2 mt-2 bg-gray-200 p-4">
        <b>Категория тегов</b> - используется для определения тегов и понимания
        для кого блока они созданы.
        <div className="mt-2">
          Например:
          <ul>
            <li>** Rockwool: Маленькие теги над фильтрами **</li>
            <li>** Черепица TEGOLA: Большие теги **</li>
          </ul>
        </div>
      </div>

      <div className="mb-2">
        <span className="text-lg">1)</span> ID данной категории необходимо
        передать ресурсу в TV поле. Заранее на сайте необходимо создать разные
        TV поля{" "}
        <span className="text-xs text-gray-500">
          (если необходимо выводить теги в разных частях сайта с отличающимся
          друг от друга дизайном)
        </span>
      </div>
      <div className="mb-2">
        <span className="text-lg">2)</span> Создаем тег
        <div className="mb-2 mt-2 bg-gray-200 p-4">
          <div>Тег может быть 2х типов:</div>
          <ul>
            <li>- Ресурс</li>
            <li>- Кастомный</li>
          </ul>
          <div className="mt-2">
            <b>Ресурс</b> - Тег в котором доступно для заполнения 2 поля{" "}
            <b>группа</b> и <b>ID ресурса</b>. Остальные поля подтянутся
            автоматически при выводе на страницу (сниппет - getTags.php)
          </div>
          <div className="mt-2">
            <b>Кастомный</b> - Тег в котором вы сами решаете что и чем заполнить
          </div>
        </div>
      </div>

      <div className="mt-4">
        <div className="text-lg text-center">Особые моменты</div>
        <div>
          - В теге вы можете указать необязательный параметр - группа.
          <p>
            Например - "Для внутренних перегородок и стен". В таком случае все
            теги у которых есть такая группа, будут выводиться в ней
          </p>
        </div>
        <div className="mt-2">
          - В одной категории тегов вы можете сочитать разные типы как
          тег-Ресурс, так и тег-Кастомный
        </div>
      </div>
    </div>
  );
}

export default function HelpModal() {
  const {openModal} = useModal();

  const handleModal = () => {
    openModal("Инструкция", <HelpContent />);
  };

  return (
    <>
      <button
        onClick={handleModal}
        type="button"
        className="text-gray-900 bg-gradient-to-r from-teal-200 to-lime-200 hover:bg-gradient-to-l hover:from-teal-200 hover:to-lime-200 focus:ring-4 focus:outline-none focus:ring-lime-200 dark:focus:ring-teal-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
      >
        Инструкция
      </button>
    </>
  );
}
