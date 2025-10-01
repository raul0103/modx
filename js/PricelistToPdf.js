/**
 * PricelistToPdf
 *
 * Для корректной работы на странице должны быть:
 * 1. Кнопки для скачивания:
 *    <button download-pricelist-btn>Скачать PDF</button>
 *
 * 2. Обёртки для таблиц:
 *    <div data-pricelister-wrapper>
 *      <h2>Заголовок таблицы</h2>
 *      <table>
 *        <tr><td>...</td></tr>
 *      </table>
 *    </div>
 *
 * 3. Элементы с контактной информацией:
 *    <span class="header-top__logo-link"><span>Город</span></span>
 *    <div class="company-address__about-text">Адрес</div>
 *    <a class="header__mobile__phone" href="tel:+79999999999">+7 (999) 999-99-99</a>
 *    <span class="header-top__contacts-mail">mail@example.com</span>
 *
 * Скрипт подключает html2pdf.js динамически и генерирует PDF со всеми таблицами.
 */

export default class PricelistToPdf {
  constructor() {
    this.downloadBtns = document.querySelectorAll("[download-pricelist-btn]");
    this.allTablesWrapper = document.querySelectorAll("[data-pricelister-wrapper]");

    this.h2p = {
      bundle:
        "https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js",
      config: {
        margin: [10, 10, 10, 10],
        filename: "pricelister.pdf",
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
      },
    };

    this.contacts = {
      logo: document.querySelector(".header-top__logo-link")?.innerHTML || "",
      address: document.querySelector(".company-address__about-text")?.textContent || "",
      phone: document.querySelector(".header__mobile__phone")?.href.substring(4) || "",
      mail: document.querySelector(".header-top__contacts-mail")?.textContent || "",
      url: window.location.protocol + "//" + window.location.host,
    };

    this.STYLES = `
        <style>
            .pdf-block { margin: 50px auto; }
            .pdf-title { margin: 30px 0 10px 0; text-align: center; }
            .pdf-table-container { margin-bottom: 40px; }
            .pdf-table-caption { text-align: center; width: 100%; margin-bottom: 10px; }
            .pdf-table { margin: 10px auto 30px; width: 90%; border-collapse: collapse; }
            .pdf-cell { border: 1px solid #999; padding: 10px 4px; background: #fff; color: #000; }
            .pdf-cell-header { background: #F6C501; font-size: 20px; text-align: center; font-weight: bold; }
        
            .pdf-header { width: 100%; border: 0; background-color: #0089B6; border-collapse: collapse; }
            .pdf-header-left { padding: 10px; border: none; }
            .pdf-logo-link { display: flex; align-items: center; column-gap: 24px; padding: 3px 0; color: #FFFFFF; font-weight: 700; font-size: 20px; text-decoration: none; }
            .pdf-logo-link img { min-width: 90px; width: auto; }
            .pdf-header-right { width: 50%; border: none; font-size: 14px; text-align: right; color: #FFFFFF; padding: 3px 0; }
            .pdf-header-right div { padding: 3px 12px 3px 0; }
            .pdf-header-link { color: #FFFFFF; text-decoration: none; }
        </style>`
  }

  init() {
    this.downloadBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        this.loadHtml2Pdf(() => {
          const wrapper = this.generateWrapper();
          const result = this.generateResult(wrapper);

          html2pdf().set(this.h2p.config).from(result).save();
        });
      });
    });
  }

  loadHtml2Pdf(callback) {
    if (window.html2pdf) {
      callback();
      return;
    }
    const scriptEl = document.createElement("script");
    scriptEl.src = this.h2p.bundle;
    scriptEl.onload = callback;
    document.body.appendChild(scriptEl);
  }

  generateResult(wrapper) {
    const date = new Date();

    const pdfBlock = document.createElement("div");
    pdfBlock.classList.add("pdf-block");

    const header = document.createElement("h2");
          header.classList.add("pdf-title");
          header.textContent = `Прайс-лист ${this.utils.currentMonth()} ${date.getFullYear()} г.`;
          pdfBlock.append(header);

    Array.from(this.allTablesWrapper).forEach((table) => {
      const container = document.createElement("div");
            container.classList.add("pdf-table-container");

      const caption = document.createElement("h3");
            caption.classList.add("pdf-table-caption");
            caption.textContent = table.querySelector("h2")?.textContent || "";
            container.append(caption);

      const tempTable = document.createElement("table");
            tempTable.classList.add("pdf-table");

      Array.from(table.querySelectorAll("tr")).forEach((tr, trIndex) => {
        const newRow = document.createElement("tr");

        Array.from(tr.children).forEach((tdElement, tdIndex) => {
          if (tdIndex === tr.children.length - 1) return; // убираем последний столбец

          const tdCell = document.createElement("td");
                tdCell.classList.add("pdf-cell");
                tdCell.textContent = tdElement.textContent;

          if (trIndex === 0) {
            tdCell.classList.add("pdf-cell-header");
          }

          newRow.append(tdCell);
        });

        tempTable.append(newRow);
      });

      container.append(tempTable);
      pdfBlock.append(container);
    });

    let resultHtml = wrapper.replaceAll("%LOGO%", this.contacts.logo);
        resultHtml = resultHtml.replaceAll("%URL%", this.contacts.url);
        resultHtml = resultHtml.replaceAll("%ADDRESS%", this.contacts.address);
        resultHtml = resultHtml.replaceAll("%PHONE%", this.contacts.phone);
        resultHtml = resultHtml.replaceAll("%MAIL%", this.contacts.mail);
        resultHtml = resultHtml.replaceAll("%TABLE%", pdfBlock.outerHTML);
        resultHtml = resultHtml.replaceAll("%STYLES%", this.STYLES);

    const resultContainer = document.createElement("div");
          resultContainer.innerHTML = resultHtml;

    return resultContainer;
  }

    generateWrapper() {
        return `
                %STYLES%
                <table class="pdf-header">
                    <tr>
                        <td class="pdf-header-left">
                            <a href="%URL%" class="pdf-logo-link"> 
                                %LOGO%
                            </a>
                        </td>
                        <td class="pdf-header-right">
                            <div>%ADDRESS%</div>
                            <div><a href="tel:%PHONE%" class="pdf-header-link">%PHONE%</a></div>
                            <div><a href="mailto:%MAIL%" class="pdf-header-link">%MAIL%</a></div>
                        </td>
                    </tr>
                </table>
                %TABLE%
            `;
    }


  utils = {
    currentMonth() {
      const date = new Date();
      const months = [
        "Январь",
        "Февраль",
        "Март",
        "Апрель",
        "Май",
        "Июнь",
        "Июль",
        "Август",
        "Сентябрь",
        "Октябрь",
        "Ноябрь",
        "Декабрь",
      ];
      return months[date.getMonth()];
    },
  };
}
