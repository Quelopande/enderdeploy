import sys
import requests
import subprocess
import os
import random
import string
import json

# Configuración inicial para Cloudflare
CLOUDFLARE_API_TOKEN = "w9HUncJf1Nw8fUwLIAjmWfNX7ALlEc_y80HyaPO2"
ZONE_ID = "2ff7857725f5e8fca6d218829286e3d1"
DOMAIN = "enderhosting.com.mx"
STATIC_IP = "209.112.76.143"
GITOPS_PATH = os.path.expanduser("~/gitops/")
FRAPPE_DOCKER_PATH = os.path.expanduser("~/frappe_docker/")
DB_ROOT_PASSWORD = "luisque122"
SUBDOMINIOS_FILE = os.path.join(GITOPS_PATH, "subdominios.json")

# Generar contraseña aleatoria para el admin
def generate_random_password(length=12):
    characters = string.ascii_letters + string.digits + string.punctuation
    return ''.join(random.choices(characters, k=length))

# Paso 1: Crear subdominio en Cloudflare
def create_cloudflare_subdomain(subdomain):
    url = f"https://api.cloudflare.com/client/v4/zones/{ZONE_ID}/dns_records"
    headers = {
        "Authorization": f"Bearer {CLOUDFLARE_API_TOKEN}",
        "Content-Type": "application/json"
    }
    payload = {
        "type": "A",
        "name": f"{subdomain}.{DOMAIN}",
        "content": STATIC_IP,
        "ttl": 1,
        "proxied": False
    }
    response = requests.post(url, headers=headers, json=payload)
    response.raise_for_status()
    print(f"Subdominio {subdomain}.{DOMAIN} creado correctamente.")

    # Guardar el subdominio en un archivo JSON
    try:
        with open(SUBDOMINIOS_FILE, "r") as file:
            subdomains = json.load(file)
    except (FileNotFoundError, json.JSONDecodeError):
        subdomains = []

    subdomains.append({
        "subdomain": f"{subdomain}.{DOMAIN}",
        "ip": STATIC_IP
    })

    with open(SUBDOMINIOS_FILE, "w") as file:
        json.dump(subdomains, file, indent=4)
    print(f"Subdominio {subdomain}.{DOMAIN} guardado en {SUBDOMINIOS_FILE}.")

# Paso 2: Actualizar archivo .env con el nuevo subdominio
def update_env_file(subdomain):
    env_file = os.path.join(GITOPS_PATH, "erpnext-one.env")
    with open(env_file, "r+") as file:
        content = file.read()
        sites_line = next(line for line in content.splitlines() if line.startswith("SITES="))
        updated_sites = sites_line.rstrip(",") + f",`{subdomain}.{DOMAIN}`"
        content = content.replace(sites_line, updated_sites)
        file.seek(0)
        file.write(content)
        file.truncate()
    print(f"Archivo .env actualizado con el subdominio {subdomain}.{DOMAIN}.")

# Paso 3: Crear el archivo YAML usando Docker Compose
def create_yaml():
    os.chdir(FRAPPE_DOCKER_PATH)
    yaml_command = [
        "docker", "compose", "--project-name", "erpnext-one",
        "--env-file", os.path.join(GITOPS_PATH, "erpnext-one.env"),
        "-f", "compose.yaml",
        "-f", "overrides/compose.redis.yaml",
        "-f", "overrides/compose.multi-bench.yaml",
        "-f", "overrides/compose.multi-bench-ssl.yaml",
        "config"
    ]
    with open(os.path.join(GITOPS_PATH, "erpnext-one.yaml"), "w") as yaml_file:
        subprocess.run(yaml_command, stdout=yaml_file, check=True)
    print("Archivo erpnext-one.yaml creado correctamente.")

# Paso 4: Actualizar los contenedores con Docker Compose
def update_containers():
    yaml_path = os.path.join(GITOPS_PATH, "erpnext-one.yaml")
    down_command = ["docker", "compose", "--project-name", "erpnext-one", "-f", yaml_path, "down"]
    up_command = ["docker", "compose", "--project-name", "erpnext-one", "-f", yaml_path, "up", "-d"]
    subprocess.run(down_command, check=True)
    subprocess.run(up_command, check=True)
    print("Contenedores actualizados correctamente.")

# Paso 5: Crear nueva aplicación para el subdominio
def create_new_site(subdomain):
    admin_password = generate_random_password()
    create_site_command = [
        "docker", "compose", "--project-name", "erpnext-one", "exec", "backend",
        "bench", "new-site",
        f"--mariadb-user-host-login-scope=%",
        f"--db-root-password={DB_ROOT_PASSWORD}",
        "--install-app", "erpnext",
        f"--admin-password={admin_password}",
        f"{subdomain}.{DOMAIN}"
    ]
    subprocess.run(create_site_command, check=True)
    print(f"Aplicación {subdomain}.{DOMAIN} creada correctamente. Contraseña de admin: {admin_password}")

# Función principal
def main():
    # Leer el subdominio desde los argumentos, si se pasa
    subdomain = sys.argv[1] if len(sys.argv) > 1 else "errorAlEncontrarVariable"  # Valor predeterminado "errorAlEncontrarVariable"
    create_cloudflare_subdomain(subdomain)
    update_env_file(subdomain)
    create_yaml()0
    update_containers()
    create_new_site(subdomain)

if __name__ == "__main__":
    main()
