<div data-city-changer class="city-changer">
  <input
    class="input"
    type="text"
    oninput="city_changer.input(this)"
    placeholder="Поиск города по названию"
  />
  <div data-city-changer-search-values></div>
  <div class="city-changer__wrapper">
    <div>
      <div class="city-changer__title">Регион</div>
      <div data-city-changer-districts></div>
    </div>

    <div>
      <div class="city-changer__title">Область/Край</div>
      <div data-city-changer-regions></div>
    </div>

    <div>
      <div class="city-changer__title">Населеный пункт</div>
      <div data-city-changer-cities></div>
    </div>
  </div>
</div>
