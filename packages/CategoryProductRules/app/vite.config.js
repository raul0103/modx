// vite.config.ts
import fs from "fs";
import { rmSync } from "fs";
import { resolve } from "path";

import { normalizePath } from "vite";
import { defineConfig } from "vite";
import { viteStaticCopy } from "vite-plugin-static-copy";

import react from "@vitejs/plugin-react";

const assets_path = "../../../../assets/components/CategoryProductRules";
const base_path = "../templates";

export default defineConfig(({ mode }) => {
  if (mode === "production") {
    // Удалить перед экспортом
    [assets_path, base_path].forEach((dir) => {
      dir = normalizePath(resolve(__dirname, dir));
      if (fs.existsSync(dir)) {
        rmSync(dir, { recursive: true, force: true });
        console.log(`✅ Удалено: ${dir}`);
      }
    });
  }
  return {
    plugins: [
      react(),

      // Копируем файлы для доступа в админке к js и css
      viteStaticCopy({
        targets: [
          {
            src: normalizePath(resolve(__dirname, base_path + "/*")),
            dest: assets_path,
          },
          {
            src: normalizePath(resolve(__dirname, "../action.php")),
            dest: assets_path,
          },
        ],
      }),
    ],
    base: mode === "production" ? "assets/components/CategoryProductRules" : "/",
    build: {
      // Папка для работы Index.html в админке
      // JS и CSS копируем через viteStaticCopy. Так как в ../templates/ они будут не видны 403
      outDir: base_path,
      emptyOutDir: true,
    },
  };
});
