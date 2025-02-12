import puppeteer from "puppeteer";
import fs from "fs";
import path from "path";
import crypto from "crypto";
import csvWriter from "csv-writer";

// Настройки
const filename = "Аксессуары для мягкой кровли Катепал";
const URL =
  "https://vestmet-shop.ru/catalog/aksessuary_k_myagkoy_krovle_katepal/";
const pages = 2;
const page_prefix = "?PAGEN_1=";
const folder_name = "vestmet";

// Создание папки для изображений
const imagesFolder = `results/${folder_name}/images`;
fs.mkdirSync(imagesFolder, { recursive: true });

// Заголовки CSV
const headers = [
  { id: "url", title: "URL" },
  { id: "title", title: "Название" },
  { id: "price", title: "Цена" },
  { id: "content", title: "Описание" },
];

// Хранилище данных
const results = [];

async function parsePage(url, browser) {
  console.log(`Парсим страницу: ${url}`);

  const page = await browser.newPage();
  await page.goto(url, { waitUntil: "domcontentloaded" });

  try {
    const data = await page.evaluate(() => {
      const title =
        document.querySelector("h1")?.innerText.trim() || "Нет названия";
      const price =
        document.querySelector('[data-currency="RUB"]')?.innerText.trim() ||
        "Нет цены";

      let contentElement = document.querySelector(".detail_text");
      if (contentElement) {
        contentElement
          .querySelectorAll("img, a[href^='tel:'], a[href^='mailto:']")
          .forEach((el) => el.remove());
      }
      const content = contentElement
        ? contentElement.innerHTML.replace(/\s+/g, " ").trim()
        : "Нет описания";

      const characteristics = {};
      document
        .querySelectorAll('[itemprop="additionalProperty"]')
        .forEach((el) => {
          const key = el.children[0]?.innerText.trim();
          const value = el.children[1]?.innerText.trim();
          if (key && value) characteristics[key] = value;
        });

      const imageLinks = Array.from(
        document.querySelectorAll(".item_slider .slides img")
      )
        .slice(0, 10)
        .map((img) => "https://vestmet-shop.ru" + img.getAttribute("src"));

      return { title, price, content, characteristics, imageLinks };
    });

    // Скачивание и хеширование изображений
    const imageNames = await Promise.all(
      data.imageLinks.map(async (imageUrl, index) => {
        const imageHash = await downloadAndHashImage(imageUrl);
        if (imageHash) {
          const imageName = `${imageHash}.jpg`;
          data[`image-${index + 1}`] = imageName;
          return { id: `image-${index + 1}`, title: `Картинка ${index + 1}` };
        }
      })
    );

    // Добавляем новые заголовки (характеристики и картинки)
    Object.keys(data.characteristics).forEach((key) => {
      if (!headers.some((h) => h.id === key))
        headers.push({ id: key, title: key });
    });

    imageNames.forEach((imageHeader) => {
      if (imageHeader && !headers.some((h) => h.id === imageHeader.id))
        headers.push(imageHeader);
    });

    results.push({ url, ...data, ...data.characteristics });
  } catch (error) {
    console.error(`Ошибка на странице ${url}:`, error);
  }

  await page.close();
}

async function scrape() {
  const browser = await puppeteer.launch({ headless: "new" });

  const categoryUrls = [URL];
  for (let i = 2; i <= pages; i++) {
    categoryUrls.push(URL + page_prefix + i);
  }

  for (const pageUrl of categoryUrls) {
    console.log(`Парсим категорию: ${pageUrl}`);

    const categoryPage = await browser.newPage();
    await categoryPage.goto(pageUrl, { waitUntil: "domcontentloaded" });

    const productLinks = await categoryPage.evaluate(() =>
      Array.from(document.querySelectorAll(".item-title a")).map(
        (a) => "https://vestmet-shop.ru" + a.getAttribute("href")
      )
    );

    await categoryPage.close();

    for (const productUrl of productLinks) {
      await parsePage(productUrl, browser);
    }
  }

  await browser.close();

  // Записываем CSV
  const csvWriterInstance = csvWriter.createObjectCsvWriter({
    path: `results/${folder_name}/${filename}.csv`,
    header: headers,
  });

  await csvWriterInstance.writeRecords(results);
  console.log("✅ CSV файл создан!");
}

// Функция для скачивания изображения и генерации хэша
async function downloadAndHashImage(url) {
  try {
    const response = await fetch(url);
    if (!response.ok) throw new Error(`Ошибка загрузки: ${url}`);

    const buffer = await response.arrayBuffer();
    const hash = crypto
      .createHash("md5")
      .update(Buffer.from(buffer))
      .digest("hex");

    const imagePath = path.join(imagesFolder, `${hash}.jpg`);

    if (!fs.existsSync(imagePath)) {
      fs.writeFileSync(imagePath, Buffer.from(buffer));
    }

    return hash;
  } catch (error) {
    console.error(`Ошибка загрузки изображения ${url}:`, error);
    return null;
  }
}

// Запуск парсинга
scrape();
