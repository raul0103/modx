{block 'properties'}{/block}

<div class="updoc">
  <h2>{$title}</h2>

  <form class="updoc__row" data-updoc-form>
    <div class="updoc__upload-window">
      <img
        class="updoc__upload-window-image"
        src="assets/images/modules/uploading-documents/upload.png"
      />

      <input
        class="updoc__upload-window-input"
        data-updoc-input="doc"
        id="doc"
        type="file"
        accept=".xls, .xlsx, .xlsm, .pdf, .doc, .docx"
      />

      <label for="doc" class="updoc__upload-window-label">
        Прикрепить файлы
      </label>

      <span
        class="updoc__upload-window-filename"
        data-updoc-filename="doc"
      ></span>
      <div class="updoc__upload-window-error" data-updoc-error="doc"></div>
    </div>
    <div class="updoc__information">
      <div>
        <b>Требования файла</b>
        <ul>
          <li>Формат: .xls, .xlsx, .xlsm, .pdf, .doc, .docx</li>
          <li>Макс. размер файла: 10 мб</li>
        </ul>
      </div>

      <div class="updoc__controls">
        <button type="button" data-modal-callback>
          {$buttons_title['callback']}
        </button>
        <button type="submit">{$buttons_title['submit']}</button>
      </div>
    </div>
  </form>
</div>
