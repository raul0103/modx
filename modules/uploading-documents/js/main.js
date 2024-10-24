/**
 * data-updoc-form - Форма с полем файла
 * data-updoc-input="<KEY>" - input[type="file"] - Аттрибут дляя основного поля с файлом
 * data-updoc-filename="<KEY>" - Поле с названием файла
 * data-updoc-error="<KEY>" - Поле с ошибкой
 *
 * После сохранения файла, открываем модалку [this.utils.other.openCallback(data.message)] с формой которая уже есть на сайте
 */

export default class UpDOC {
  file = null;
  cache = { elements: {}, fetch_result: {}, form: {} };
  rules = {
    size: {
      value: 1024 * 1024 * 10, // 10 MB
      error: "Ошибка: Размер файла больше 10 МБ",
    },
    extension: {
      value: ["xls", "xlsx", "xlsm", "pdf", "doc", "docx"], // Допустимые форматы
      error: "Ошибка: Расширение файла не подходит",
    },
  };
  file = null;

  init() {
    document.querySelectorAll("[data-updoc-form]").forEach((form) => {
      form.addEventListener("submit", this.events.submit);
    });

    document.querySelectorAll("[data-updoc-input]").forEach((input) => {
      input.addEventListener("change", this.events.change);
    });
  }

  events = {
    submit: (e) => {
      e.preventDefault();

      let input = e.target.querySelector("[data-updoc-input]");
      if (!input) {
        console.error("Не найдено поле с файлом");
        return;
      }

      if (!this.file) {
        alert("Необходимо прикрепить файл к письму");
        return;
      }

      const form_data = new FormData();
      form_data.append("file", this.file);
      form_data.append("action", "test-action");

      // Кэширование убирает бесконечную отправку формы если файл один и тот же
      let hash = this.file.name + this.file.size;
      if (this.cache.fetch_result[hash]) {
        this.utils.other.openCallback(this.cache.fetch_result[hash]);
      } else {
        fetch("/assets/modules/uploading-documents/connector.php", {
          method: "POST",
          body: form_data,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.status == true) {
              this.cache.fetch_result[hash] = data.message;
              let file_saved_path = data.message;
              this.utils.other.openCallback(file_saved_path);
            } else {
              delete this.cache.fetch_result[hash];
              alert(data.message);
            }
          });
      }
    },
    change: (e) => {
      let key = e.target.dataset.updocInput;
      let form = e.target.form;

      this.file = null;
      this.utils.file.setName(form, key, null);

      let file = e.target.files[0];
      if (!file) {
        return;
      }

      let valid = this.utils.file.vaidate(form, key, file);
      if (!valid) {
        e.target.value = "";
        return;
      }

      this.utils.file.setName(form, key, file.name);

      this.file = file;
    },
  };

  utils = {
    file: {
      setName: (form, key, file_name) => {
        let cache_name = `filename_${key}`;
        if (!this.cache.elements[cache_name]) {
          this.cache.elements[cache_name] = form.querySelector(
            `[data-updoc-filename="${key}"]`
          );
        }
        let element = this.cache.elements[cache_name];

        if (file_name) {
          element.classList.add("show");
          element.innerHTML = `Файл: ${file_name}`;
        } else {
          element.classList.remove("show");
          element.innerHTML = "";
        }
      },
      setErrors: (form, key, errors) => {
        let cache_name = `error_${key}`;
        if (!this.cache.elements[cache_name]) {
          this.cache.elements[cache_name] = form.querySelector(
            `[data-updoc-error="${key}"]`
          );
        }
        let element = this.cache.elements[cache_name];

        if (errors.length > 0) {
          element.classList.add("show");
          element.innerHTML = errors.join("<br>");
          return false;
        } else {
          element.classList.remove("show");
          element.innerHTML = "";
          return true;
        }
      },
      vaidate: (form, key, file) => {
        let errors = [];

        if (file.size > this.rules.size.value)
          errors.push(this.rules.size.error);

        let file_extension = file.name.split(".").pop();
        if (this.rules.extension.value.indexOf(file_extension) == -1)
          errors.push(this.rules.extension.error);

        this.utils.file.setErrors(form, key, errors);

        if (errors.length > 0) {
          return false;
        } else {
          return true;
        }
      },
    },
    other: {
      openCallback: (file_url) => {
        let modal = document.querySelector(".callbackTop");

        if (modal) {
          modal.style.cssText =
            "display:block;left: 0; right: 0; top: 0; bottom: 0; height: max-content; margin: auto;";

          let modal_form = modal.querySelector("form");
          if (!modal_form) return;

          if (this.cache.form.input && this.cache.form.notice) {
            this.cache.form.input.value = `Прикрепленный файл: ${file_url}`;
            this.cache.form.notice.href = file_url;
          } else {
            let input = document.createElement("input");
            input.name = "message";
            input.type = "hidden";
            input.value = `Прикрепленный файл: ${file_url}`;
            modal_form.appendChild(input);

            let notice = document.createElement("a");
            notice.href = file_url;
            notice.className = "form-notice-link";
            notice.target = "_blank";
            notice.textContent = "Прикрепленный файл";
            modal_form.appendChild(notice);

            this.cache.form.input = input;
            this.cache.form.notice = notice;
          }
        }

        let overlay = document.querySelector(".callbackFon");
        if (overlay) {
          overlay.style.display = "block";
        }
      },
    },
  };
}
