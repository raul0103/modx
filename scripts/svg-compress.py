import re
import base64
import argparse
from io import BytesIO
from PIL import Image

IMG_RE = re.compile(
    r'(href|xlink:href)=["\']data:image/(png|jpeg|jpg);base64,([^"\']+)["\']'
)

def compress_base64_img(b64_data, img_type="jpeg", quality=60, convert_to="jpeg"):
    raw = base64.b64decode(b64_data)
    img = Image.open(BytesIO(raw))

    out = BytesIO()

    if convert_to == "jpeg":
        if img.mode in ("RGBA", "LA"):
            bg = Image.new("RGB", img.size, (255, 255, 255))
            bg.paste(img, mask=img.split()[-1])
            img = bg
        else:
            img = img.convert("RGB")

        img.save(out, format="JPEG", quality=quality, optimize=True)
        new_type = "jpeg"

    elif convert_to == "webp":
        img.save(out, format="WEBP", quality=quality, method=6)
        new_type = "webp"

    else:
        return b64_data, img_type

    new_b64 = base64.b64encode(out.getvalue()).decode("utf-8")
    return new_b64, new_type

def process_svg(path_in, path_out, quality=60, convert_to="jpeg"):
    print("→ Чтение SVG:", path_in)
    with open(path_in, "r", encoding="utf-8") as f:
        svg = f.read()

    found = IMG_RE.findall(svg)
    print("→ Поиск изображений...")

    if not found:
        print("❗ Встроенных base64 изображений НЕ найдено.")
        return

    print(f"✓ Найдено изображений: {len(found)}")

    def repl(match):
        attr = match.group(1)
        img_type = match.group(2)
        data = match.group(3)

        print(f"→ Сжатие {img_type}...")

        new_data, new_type = compress_base64_img(
            data,
            img_type=img_type,
            quality=quality,
            convert_to=convert_to
        )

        print(f"   ✓ Готово: {img_type} → {new_type}")

        return f'{attr}="data:image/{new_type};base64,{new_data}"'

    new_svg = IMG_RE.sub(repl, svg)

    with open(path_out, "w", encoding="utf-8") as f:
        f.write(new_svg)

    print("✔ Сжатый SVG сохранён:", path_out)

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("input")
    parser.add_argument("output")
    parser.add_argument("--quality", type=int, default=60)
    parser.add_argument("--format", choices=["jpeg", "webp"], default="jpeg")
    args = parser.parse_args()

    print("→ Запуск с параметрами:")
    print("  input:", args.input)
    print("  output:", args.output)
    print("  quality:", args.quality)
    print("  format:", args.format)

    process_svg(
        args.input,
        args.output,
        quality=args.quality,
        convert_to=args.format
    )
