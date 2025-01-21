<div class="cc-modal" id="ccm-modal-changer">
  <div class="cc-modal__city-name">
    Ваш город <span id="ccm-city-name"></span>?
    <div class="cc-modal__info">
      От выбранного города зависит ассортимент, наличие, условия доставки.
    </div>
  </div>
  <div class="cc-modal__controls">
    <button id="ccm-confirm-btn">Да</button>
    <button id="ccm-city-list-btn">Изменить</button>
  </div>
</div>

<div class="cc-modal-list" id="ccm-modal-city-list">
  <div class="cc-modal-list__overlay" id="ccm-modal-city-list-overlay"></div>

  <div class="cc-modal-list__container">
    <div class="cc-modal-list__header">
      Выберите ваш город
      <span class="cc-modal-list__btn-close" id="ccm-modal-close-btn"></span>
    </div>
    <ul class="cc-modal-list__items">
      <li
        class="cc-modal-list__item"
        data-city-subdomain=""
        data-city-name="Санкт-Петербург"
      >
        Санкт-Петербург и область
      </li>
      <li
        class="cc-modal-list__item"
        data-city-subdomain="msk"
        data-city-name="Москва"
      >
        Москва и область
      </li>
      <li
        class="cc-modal-list__item"
        data-city-subdomain="krasnodar"
        data-city-name="Краснодар"
      >
        Краснодарский край
      </li>
    </ul>
  </div>
</div>
