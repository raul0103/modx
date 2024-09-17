import paramiko
from data import domains, subdomains
from config import SSH_HOST, SSH_PORT, SSH_USER, SSH_PASS, HESTIA_USER

domains = [
    "ooo-metall-stroy.ru",
    "www-knauf.ru",
    "izomin-msk.ru",
    "izovol-msk.ru",
    "www-bonolit.ru",
    "hotrock-msk.ru",
    "msk-rockwool.ru",
    "www-tn.ru",
    "penoplex-msk.ru",
    "isover-msk.ru",
    "paroc-msk.ru",
    "ursa-msk.ru",
    "stena-m.ru",
    "sklad-nk.ru",
    "fasad-pro.ru",
    "vladles33.ru",
    "plitnye-materialy.ru",
    "mdvp-plita.ru",
    "isoroc-msk.ru",
    "umatex-msk.ru",
    "www-docke.ru",
    "www-metallprofil.ru",
    "www-katepal.ru",
    "www-grandline.ru",
    "www-braas.ru",
    "www-tegola.ru",
    "tekhnonikol-shinglas.ru",
]
subdomains = [
    "sochi",
    "novorossijsk",
    "labinsk",
    "severnyj",
    "krymsk",
    "gelendzhik",
    "majkop",
    "armavir",
    "belorechensk",
    "ejsk",
    "slavyansk-na-kubani",
    "tihoreck",
    "zapadnyj",
    "timashevsk",
    "gulkevichi",
    "temryuk",
    "goryachij-klyuch",
    "tuapse",
    "afipskij",
    "korenovsk",
    "kropotkin",
    "abinsk",
    "kurganinsk",
    "krasnoselskoe",
    "ust-labinsk",
    "chernomorskoe",
    "apsheronsk",
    "stanica-severskaya",
    "krasnodar"
]


client = paramiko.SSHClient()
client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
client.connect(hostname=SSH_HOST, username=SSH_USER, password=SSH_PASS, port=SSH_PORT)

def ssh_command(command):
    stdin, stdout, stderr = client.exec_command(command)
    data = stdout.read() + stderr.read()
    print(data)

#добавление алиасов к домену aliases строкой через пробел
def add_aliases_domain(domain, aliases):
    stdin, stdout, stderr = client.exec_command(f'export PATH=$PATH:/usr/local/hestia/bin && v-add-web-domain-alias {HESTIA_USER} {domain} {aliases}')
    data = stdout.read() + stderr.read()
    print(data)

def add_domain(domain):
    stdin, stdout, stderr = client.exec_command(f'export PATH=$PATH:/usr/local/hestia/bin && v-add-domain {HESTIA_USER} {domain}')
    data = stdout.read() + stderr.read()
    print(data)

def add_letsencrypt_domain(domain, aliases):
    #aliases через запятую ","
    stdin, stdout, stderr = client.exec_command(
        f'export PATH=$PATH:/usr/local/hestia/bin && v-add-letsencrypt-domain {HESTIA_USER} {domain} {aliases}')
    data = stdout.read() + stderr.read()
    print(data)


#ssh_command("echo $PATH")
#add_domain("anapa.izomin-msk.ru")

for domain in domains[2:]:
    new_domain = f"anapa.{domain}"
    print(new_domain)
    #add_domain(new_domain)
    # поддомены
    aliases = f".{domain},".join(subdomains) + "." + domain
    print(f".{domain},".join(subdomains) + "." + domain)
    add_letsencrypt_domain(new_domain, aliases)

        #new_alias = f"{subdomain}.{domain}"
        #print(f"   {new_alias}")
        #add_aliases_domain(new_domain, new_alias)
        #add_letsencrypt_domain(new_domain, new_alias)

#
client.close()
