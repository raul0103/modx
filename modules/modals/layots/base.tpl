{block 'params'}{/block}

<div class="modal modal-callback" id="{$id}">
  <div class="modal-overlay" data-modal-close></div>

  <div class="modal-container">
    <div data-modal-body-before>
      <div class="modal-callback__header">
        <div class="modal-title fs-25-18 fw-700">{$title}</div>
      </div>
      <div class="modal-callback__body">{block 'body'}{/block}</div>
    </div>

    <div data-modal-body-after>
      <div class="modal-callback__header">
        <div class="modal-title fs-25-18 fw-700">Спасибо за обращение!</div>
      </div>
      <div class="modal-callback__body">Ожидайте ответа менеджера</div>
    </div>

    <div class="modal-icon-close" data-modal-close></div>
  </div>
</div>
