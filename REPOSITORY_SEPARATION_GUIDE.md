# Guía de Separación de Repositorios

## 📚 Contexto del Proyecto

El proyecto final requiere **dos repositorios separados**:
1. **Repositorio de Aplicación** (Este) - Código fuente de la API
2. **Repositorio de Infraestructura** (Nuevo) - Configuración de despliegue y CI/CD

---

## 🎯 Objetivos de la Separación

### ✅ Ventajas
- **Separación de responsabilidades**: Código vs Infraestructura
- **Control de acceso diferenciado**: Desarrolladores vs DevOps
- **Versionamiento independiente**: Cambios en infra no afectan la app
- **Seguridad**: Variables sensibles solo en repositorio de infraestructura
- **Reutilización**: La misma infra puede servir múltiples aplicaciones

---

## 📦 Repositorio 1: Aplicación (api-servidores)

### ✅ Debe Contener

**Código Fuente:**
```
app/                          # Lógica de la aplicación
├── Http/
│   ├── Controllers/
│   └── Requests/
└── Models/

routes/                       # Definición de rutas
config/                       # Configuración de Laravel
database/                     # Migraciones y seeders
├── factories/
├── migrations/
└── seeders/

tests/                        # Pruebas automatizadas
├── Feature/
└── Unit/

public/                       # Archivos públicos
resources/                    # Vistas y assets
storage/                      # Archivos generados
bootstrap/                    # Archivos de inicio
vendor/                       # Dependencias (no versionado)
```

**Archivos de Configuración de la Aplicación:**
```
composer.json                 # Dependencias PHP
composer.lock                 # Versiones exactas
package.json                  # Dependencias JS (si aplica)
phpunit.xml                   # Configuración de pruebas
phpstan.neon                  # Configuración de análisis estático
sonar-project.properties      # Configuración de SonarQube
.env.example                  # Plantilla de variables de entorno
artisan                       # CLI de Laravel
```

**Archivos Docker de la Aplicación:**
```
Dockerfile                    # SOLO la imagen de la aplicación
.dockerignore                 # Exclusiones del build
```

**Documentación:**
```
README.md                     # Documentación principal
API_EXAMPLES.md              # Ejemplos de uso
PROJECT_SUMMARY.md           # Resumen técnico
```

### ❌ NO Debe Contener

- ❌ `docker-compose.yml` (va a infraestructura)
- ❌ Configuración de Nginx (va a infraestructura)
- ❌ Playbooks de Ansible (va a infraestructura)
- ❌ Pipelines de CI/CD (va a infraestructura)
- ❌ Variables de entorno de producción (va a infraestructura)

---

## 🏗️ Repositorio 2: Infraestructura (Nuevo - crear)

### Estructura Recomendada

```
proyecto-laravel-infra/
├── README.md                          # Documentación de infraestructura
├── docker-compose.yml                 # Orquestación de servicios
├── docker-compose.prod.yml            # Override para producción
│
├── docker/
│   ├── nginx/
│   │   ├── nginx.conf                 # Configuración de Nginx
│   │   └── ssl/                       # Certificados SSL (gitignored)
│   └── php/
│       └── custom.ini                 # Configuración PHP custom
│
├── ansible/
│   ├── inventory/
│   │   ├── hosts.yml                  # Inventario de servidores
│   │   └── group_vars/
│   │       ├── production.yml
│   │       └── staging.yml
│   ├── playbooks/
│   │   ├── deploy.yml                 # Playbook de despliegue
│   │   ├── setup.yml                  # Configuración inicial del servidor
│   │   └── rollback.yml               # Rollback
│   └── roles/
│       ├── docker/                    # Instalación de Docker
│       ├── nginx/                     # Configuración de Nginx
│       └── app/                       # Despliegue de la app
│
├── .github/
│   └── workflows/
│       ├── deploy-staging.yml         # CI/CD para staging
│       ├── deploy-production.yml      # CI/CD para producción
│       └── run-tests.yml              # Ejecutar tests en PR
│
├── terraform/                         # (Opcional) IaC
│   ├── main.tf
│   ├── variables.tf
│   └── outputs.tf
│
├── scripts/
│   ├── deploy.sh                      # Script de despliegue
│   ├── backup.sh                      # Script de backup
│   └── health-check.sh                # Verificar salud de la app
│
└── environments/
    ├── .env.staging                   # Variables de staging
    ├── .env.production                # Variables de producción
    └── secrets/                       # Secretos (gitignored)
```

### 🔑 Contenido Detallado

#### `docker-compose.yml`
```yaml
version: '3.8'

services:
  app:
    image: ghcr.io/sebainzulza/api-servidores:${VERSION:-latest}
    # No usa 'build', usa imagen ya construida
    environment:
      - APP_ENV=${APP_ENV}
      - DB_HOST=${DB_HOST}
    volumes:
      - storage-data:/var/www/storage

  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf
      - ssl-certs:/etc/nginx/ssl

  db:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
    volumes:
      - mysql-data:/var/lib/mysql

volumes:
  storage-data:
  ssl-certs:
  mysql-data:
```

#### `.github/workflows/deploy-production.yml`
```yaml
name: Deploy to Production

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout Infrastructure Repo
        uses: actions/checkout@v3

      - name: Deploy with Ansible
        run: |
          ansible-playbook -i ansible/inventory/hosts.yml \
            ansible/playbooks/deploy.yml \
            --extra-vars "version=${{ github.sha }}"
        env:
          ANSIBLE_HOST_KEY_CHECKING: False
```

#### `ansible/playbooks/deploy.yml`
```yaml
---
- name: Deploy API Laravel
  hosts: production
  become: yes
  
  vars:
    app_image: "ghcr.io/sebainzulza/api-servidores:{{ version }}"
    
  tasks:
    - name: Pull latest Docker image
      docker_image:
        name: "{{ app_image }}"
        source: pull
        
    - name: Stop old containers
      docker_compose:
        project_src: /opt/api-servidores
        state: absent
        
    - name: Start new containers
      docker_compose:
        project_src: /opt/api-servidores
        state: present
        pull: yes
        
    - name: Run migrations
      docker_container:
        name: laravel-api-migrate
        image: "{{ app_image }}"
        command: php artisan migrate --force
        detach: no
```

---

## 🔀 Proceso de Migración

### Paso 1: Crear Repositorio de Infraestructura

```bash
# En GitHub, crear nuevo repo: proyecto-laravel-infra
git clone https://github.com/sebainzulza/proyecto-laravel-infra.git
cd proyecto-laravel-infra
```

### Paso 2: Copiar Archivos desde el Repositorio de Aplicación

```bash
# Desde el repo de la aplicación
cd proyecto-laravel-api/api-servidores

# Crear estructura en repo de infra
mkdir -p ../../../proyecto-laravel-infra/docker/nginx

# Copiar archivos
cp docker-compose.yml ../../../proyecto-laravel-infra/
cp docker/nginx/nginx.conf ../../../proyecto-laravel-infra/docker/nginx/
cp .env.docker ../../../proyecto-laravel-infra/environments/.env.example
```

### Paso 3: Eliminar Archivos del Repositorio de Aplicación

```bash
# En el repo de la aplicación
cd proyecto-laravel-api/api-servidores

# Eliminar archivos que van a infraestructura
git rm docker-compose.yml
git rm -r docker/
git rm .env.docker

# Commit
git commit -m "Move infrastructure files to separate repository"
git push
```

### Paso 4: Actualizar README de Aplicación

Actualizar el README.md para referenciar el repositorio de infraestructura:

```markdown
## 🚀 Despliegue

Para desplegar esta aplicación, consulta el repositorio de infraestructura:
**https://github.com/sebainzulza/proyecto-laravel-infra**
```

### Paso 5: Configurar el Repositorio de Infraestructura

```bash
cd proyecto-laravel-infra

# Crear estructura completa
mkdir -p ansible/{inventory,playbooks,roles}
mkdir -p .github/workflows
mkdir -p scripts
mkdir -p environments

# Commit inicial
git add .
git commit -m "Initial infrastructure setup"
git push
```

---

## 🔄 Flujo de Trabajo Completo

### Desarrollo de Nueva Funcionalidad

1. **Repositorio de Aplicación**:
   ```bash
   # Desarrollador trabaja en feature
   git checkout -b feature/nueva-funcionalidad
   # ... código ...
   git push origin feature/nueva-funcionalidad
   # Crear Pull Request
   ```

2. **CI ejecuta automáticamente** (en repo de aplicación):
   - Pruebas unitarias
   - Análisis estático (PHPStan)
   - Build de Docker
   - Push a Container Registry

3. **Repositorio de Infraestructura**:
   ```bash
   # Trigger manual o automático
   # Ansible despliega nueva versión
   ansible-playbook -i inventory/hosts.yml playbooks/deploy.yml
   ```

### Cambio en Infraestructura

1. **Repositorio de Infraestructura**:
   ```bash
   git checkout -b config/update-nginx
   # Modificar docker/nginx/nginx.conf
   git push origin config/update-nginx
   ```

2. **CD ejecuta**:
   - Validación de sintaxis
   - Aplicación de cambios con Ansible
   - Health check

---

## 📋 Checklist de Migración

### Repositorio de Aplicación
- [ ] Código fuente completo
- [ ] Pruebas automatizadas
- [ ] Dockerfile de la aplicación
- [ ] .dockerignore
- [ ] composer.json y composer.lock
- [ ] phpstan.neon
- [ ] sonar-project.properties
- [ ] .env.example
- [ ] README.md actualizado

### Repositorio de Infraestructura
- [ ] docker-compose.yml
- [ ] Configuración de Nginx
- [ ] Playbooks de Ansible
- [ ] Pipelines de CI/CD (.github/workflows)
- [ ] Variables de entorno por ambiente
- [ ] Scripts de despliegue
- [ ] Documentación de infraestructura

---

## 🎓 Para la Entrega del Proyecto

### Documentación Requerida

1. **README del Repositorio de Aplicación**:
   - Descripción de la API
   - Instrucciones de desarrollo local
   - Cómo ejecutar pruebas
   - Referencia al repo de infraestructura

2. **README del Repositorio de Infraestructura**:
   - Arquitectura de despliegue
   - Cómo configurar el pipeline
   - Variables de entorno requeridas
   - Proceso de despliegue manual
   - Troubleshooting

3. **Evidencias para Presentación**:
   - Screenshots del pipeline ejecutándose
   - Capturas de SonarQube
   - Logs de despliegue con Ansible
   - API funcionando en producción

---

## 🔗 Referencias

- **Repositorio de Aplicación**: https://github.com/sebainzulza/proyecto-laravel-api
- **Repositorio de Infraestructura**: https://github.com/sebainzulza/proyecto-laravel-infra (crear)

---

## 💡 Consejos Finales

1. **No versionar secretos**: Usar variables de entorno o servicios como AWS Secrets Manager
2. **Documentar todo**: Cada decisión de arquitectura debe estar documentada
3. **Automatizar al máximo**: Desde tests hasta despliegue
4. **Monitoreo**: Implementar health checks y logs centralizados
5. **Rollback plan**: Tener siempre un plan B
