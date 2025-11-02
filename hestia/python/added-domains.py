# Скрипт создает домены в hestia
# + привязывает к ним aliases

# !!! Максимальное кол-во asliases < 100

# После работы скрипта необходимо настроить созданный домен (версия php, путь к сайту)
# Что-бы все изменения вступили в силу после работы скрипта необходимо сохранить созданный домен

import paramiko
import logging
import sys

# ---------------- Настройки ---------------- #
SSH_HOST = ""
SSH_PORT = 22
SSH_USER = ""
SSH_PASS = ""

HESTIA_USER = ""

domains = [
    "root-1.www-isotecti.ru"
]
aliases = [
    "agalatovo.www-isotecti.ru",
    "beloostrov.www-isotecti.ru",
    "boksitogorsk.www-isotecti.ru",
]

# ---------------- Логирование ---------------- #
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.FileHandler("hestia_script.log", encoding="utf-8"),
        logging.StreamHandler(sys.stdout)
    ]
)

# ---------------- Подключение к SSH ---------------- #
try:
    logging.info(f"Подключение к {SSH_HOST}:{SSH_PORT} как {SSH_USER}...")
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect(hostname=SSH_HOST, username=SSH_USER,
                   password=SSH_PASS, port=SSH_PORT, timeout=10)
    logging.info("✅ Подключение установлено.")
except Exception as e:
    logging.error(f"❌ Ошибка подключения к SSH: {e}")
    sys.exit(1)


def ssh_command(command: str) -> str:
    """Выполняет SSH-команду и возвращает stdout/stderr."""
    logging.info(f"➡ Выполнение команды: {command}")
    try:
        stdin, stdout, stderr = client.exec_command(command)
        out = stdout.read().decode().strip()
        err = stderr.read().decode().strip()

        if out:
            logging.info(f"🟢 STDOUT: {out}")
        if err:
            logging.warning(f"🟠 STDERR: {err}")
        return out + "\n" + err
    except Exception as e:
        logging.error(f"❌ Ошибка при выполнении команды: {e}")
        return ""


def add_domain(domain: str):
    """Добавить домен в Hestia"""
    cmd = f'export PATH=$PATH:/usr/local/hestia/bin && v-add-domain {HESTIA_USER} {domain}'
    return ssh_command(cmd)


def add_aliases_domain(domain: str, aliases: str):
    """Добавить алиасы к домену"""
    cmd = f'export PATH=$PATH:/usr/local/hestia/bin && v-add-web-domain-alias {HESTIA_USER} {domain} {aliases}'
    return ssh_command(cmd)


def add_letsencrypt_domain(domain: str, aliases: str):
    """Добавить SSL Let's Encrypt для домена"""
    cmd = f'export PATH=$PATH:/usr/local/hestia/bin && v-add-letsencrypt-domain {HESTIA_USER} {domain} {aliases}'
    return ssh_command(cmd)


# ---------------- Основная логика ---------------- #
try:
    for domain in domains:
        new_domain = f"{domain}"
        logging.info(f"⚙️ Обработка домена: {new_domain}")

        aliases = ",".join([alias for alias in aliases])
        logging.info(f"🔗 Алиасы: {aliases}")

        # Пример последовательности:
        add_domain(new_domain)
        add_aliases_domain(new_domain, aliases)
        add_letsencrypt_domain(new_domain, aliases)

except Exception as e:
    logging.error(f"❌ Неожиданная ошибка в основном цикле: {e}")

finally:
    client.close()
    logging.info("🔒 SSH соединение закрыто.")
