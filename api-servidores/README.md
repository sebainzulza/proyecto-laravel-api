# API de Inventario de Servidores - Proyecto Final

> **Proyecto Universitario**: Desarrollo de API RESTful con CI/CD Automatizado

API RESTful desarrollada con Laravel para gestionar un inventario de servidores. Este proyecto implementa operaciones CRUD completas con validación robusta, tipado estricto, pruebas automatizadas y análisis de calidad de código.

## 📋 Características

- ✅ API RESTful con operaciones CRUD completas
- ✅ Validación de datos con Form Requests separados
- ✅ Tipado estricto en todo el código (`declare(strict_types=1)`)
- ✅ Pruebas automatizadas con PHPUnit (100% cobertura de endpoints)
- ✅ Análisis estático con PHPStan/Larastan (Nivel 5)
- ✅ Integración con SonarQube para métricas de calidad
- ✅ Dockerización completa (PHP-FPM + Nginx + MySQL)
- ✅ Respuestas JSON con códigos HTTP apropiados
- ✅ Patrón MVC de Laravel

## 🛠️ Tecnologías

- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Base de Datos**: SQLite (testing), compatible con MySQL/PostgreSQL
- **Testing**: PHPUnit 11.x

## 📦 Instalación

### Prerrequisitos

- PHP 8.2 o superior
- Composer
- Extensiones PHP requeridas: `pdo_sqlite`, `sqlite3`, `fileinfo`

### Pasos de Instalación

```bash
# 1. Clonar el repositorio
git clone https://github.com/sebainzulza/proyecto-laravel-api.git
cd proyecto-laravel-api/api-servidores

# 2. Instalar dependencias de producción
composer install --no-dev --optimize-autoloader

# 3. Instalar dependencias de desarrollo (para testing y análisis)
composer install

# 4. Copiar y configurar el archivo de entorno
cp .env.example .env

# 5. Generar clave de aplicación
php artisan key:generate

# 6. Configurar base de datos en .env
# Para SQLite (desarrollo):
# DB_CONNECTION=sqlite
# DB_DATABASE=/ruta/completa/database.sqlite

# Para MySQL (producción):
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=servers_api
# DB_USERNAME=tu_usuario
# DB_PASSWORD=tu_contraseña

# 7. Ejecutar migraciones
php artisan migrate

# 8. (Opcional) Crear datos de prueba
php artisan db:seed

# 9. Iniciar servidor de desarrollo
php artisan serve
```

La API estará disponible en `http://localhost:8000`

## 🚀 Endpoints de la API

### Base URL
```
http://localhost:8000/api
```

### 1. Listar Todos los Servidores

**GET** `/api/servers`

**Respuesta Exitosa (200)**:
```json
{
  "data": [
    {
      "id": 1,
      "name": "Production Server",
      "ip_address": "192.168.1.100",
      "status": true,
      "created_at": "2025-12-02T18:00:00.000000Z",
      "updated_at": "2025-12-02T18:00:00.000000Z"
    }
  ]
}
```

### 2. Crear un Servidor

**POST** `/api/servers`

**Body**:
```json
{
  "name": "Production Server",
  "ip_address": "192.168.1.100",
  "status": true
}
```

**Validación**:
- `name`: requerido, string, único, máximo 255 caracteres
- `ip_address`: requerido, formato IPv4 válido
- `status`: opcional, boolean (default: true)

**Respuesta Exitosa (201)**:
```json
{
  "data": {
    "id": 1,
    "name": "Production Server",
    "ip_address": "192.168.1.100",
    "status": true,
    "created_at": "2025-12-02T18:00:00.000000Z",
    "updated_at": "2025-12-02T18:00:00.000000Z"
  },
  "message": "Server created successfully"
}
```

**Respuesta de Error (422)**:
```json
{
  "message": "The name field is required. (and 1 more error)",
  "errors": {
    "name": ["The name field is required."],
    "ip_address": ["The ip address field must be a valid IP address."]
  }
}
```

### 3. Obtener un Servidor Específico

**GET** `/api/servers/{id}`

**Respuesta Exitosa (200)**:
```json
{
  "data": {
    "id": 1,
    "name": "Production Server",
    "ip_address": "192.168.1.100",
    "status": true,
    "created_at": "2025-12-02T18:00:00.000000Z",
    "updated_at": "2025-12-02T18:00:00.000000Z"
  }
}
```

**Respuesta de Error (404)**:
```json
{
  "message": "Not Found"
}
```

### 4. Actualizar un Servidor

**PUT/PATCH** `/api/servers/{id}`

**Body** (todos los campos son opcionales):
```json
{
  "name": "Updated Server",
  "ip_address": "192.168.1.200",
  "status": false
}
```

**Validación**:
- `name`: opcional, string, único (excepto el registro actual), máximo 255 caracteres
- `ip_address`: opcional, formato IPv4 válido
- `status`: opcional, boolean

**Respuesta Exitosa (200)**:
```json
{
  "data": {
    "id": 1,
    "name": "Updated Server",
    "ip_address": "192.168.1.200",
    "status": false,
    "created_at": "2025-12-02T18:00:00.000000Z",
    "updated_at": "2025-12-02T18:05:00.000000Z"
  },
  "message": "Server updated successfully"
}
```

### 5. Eliminar un Servidor

**DELETE** `/api/servers/{id}`

**Respuesta Exitosa (200)**:
```json
{
  "message": "Server deleted successfully"
}
```

**Respuesta de Error (404)**:
```json
{
  "message": "Not Found"
}
```

## 🧪 Pruebas y Calidad de Código

### Pruebas Automatizadas

El proyecto incluye pruebas automatizadas completas que cubren:
- ✅ Listar servidores
- ✅ Crear servidor exitosamente
- ✅ Validación al crear (nombre requerido, IP válida, nombre único)
- ✅ Obtener servidor específico
- ✅ Error 404 al buscar servidor inexistente
- ✅ Actualizar servidor
- ✅ Eliminar servidor
- ✅ Error 404 al eliminar servidor inexistente

**Ejecutar las Pruebas:**

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar solo las pruebas de la API de servidores
php artisan test --filter ServerApiTest

# Ejecutar con cobertura detallada
php artisan test --coverage

# Ejecutar con reporte HTML de cobertura
php artisan test --coverage-html coverage
```

**Resultados esperados:**
- ✅ 10 pruebas pasadas
- ✅ 52 assertions exitosas
- ✅ 100% de cobertura de endpoints

### Análisis Estático de Código

El proyecto utiliza **PHPStan** con la extensión **Larastan** configurado en **nivel 5**.

**Instalar herramientas de análisis:**

```bash
composer require --dev phpstan/phpstan nunomaduro/larastan
```

**Ejecutar análisis estático:**

```bash
# Ejecutar PHPStan
vendor/bin/phpstan analyse

# Ejecutar con nivel específico
vendor/bin/phpstan analyse --level=5

# Generar reporte detallado
vendor/bin/phpstan analyse --error-format=table
```

**Configuración:** El archivo `phpstan.neon` está configurado para analizar:
- ✅ Directorio `app/`
- ✅ Directorio `config/`
- ✅ Directorio `database/`
- ✅ Directorio `routes/`

### Análisis de Calidad con SonarQube

El proyecto incluye configuración para SonarQube.

**Ejecutar análisis:**

```bash
# Con SonarScanner instalado localmente
sonar-scanner

# Con Docker
docker run --rm \
  -e SONAR_HOST_URL="http://localhost:9000" \
  -e SONAR_LOGIN="tu-token" \
  -v "$(pwd):/usr/src" \
  sonarsource/sonar-scanner-cli
```

**Métricas evaluadas:**
- Duplicación de código
- Complejidad ciclomática
- Deuda técnica
- Code smells
- Bugs potenciales
- Vulnerabilidades de seguridad

## 📂 Estructura del Proyecto

```
api-servidores/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── ServerController.php    # Controlador de la API
│   │   └── Requests/
│   │       ├── StoreServerRequest.php      # Validación para crear
│   │       └── UpdateServerRequest.php     # Validación para actualizar
│   └── Models/
│       └── Server.php                      # Modelo Eloquent
├── database/
│   ├── factories/
│   │   └── ServerFactory.php               # Factory para testing
│   └── migrations/
│       └── 2025_12_02_*_create_servers_table.php
├── routes/
│   └── api.php                             # Rutas de la API
├── tests/
│   └── Feature/
│       └── ServerApiTest.php               # Tests de la API
└── README.md
```

## 🗄️ Esquema de Base de Datos

### Tabla: `servers`

| Columna      | Tipo         | Descripción                      |
|--------------|--------------|----------------------------------|
| id           | BIGINT       | ID autoincremental (PK)          |
| name         | VARCHAR(255) | Nombre del servidor (único)      |
| ip_address   | VARCHAR(255) | Dirección IPv4                   |
| status       | BOOLEAN      | Estado activo/inactivo (default: true) |
| created_at   | TIMESTAMP    | Fecha de creación                |
| updated_at   | TIMESTAMP    | Fecha de última actualización    |

## 🔧 Configuración Adicional

### Variables de Entorno (.env)

```env
APP_NAME="Server Inventory API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
# Para SQLite en producción:
# DB_DATABASE=/path/to/database.sqlite

# Para MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=servers_api
# DB_USERNAME=root
# DB_PASSWORD=
```

## 📝 Notas Técnicas

### Tipado Estricto
Todo el código implementa `declare(strict_types=1)` y utiliza type hints en:
- Parámetros de funciones
- Valores de retorno
- Propiedades de clase (cuando es aplicable)

### Validación Separada
La lógica de validación está separada del controlador usando Form Requests:
- `StoreServerRequest`: Validación para crear servidores
- `UpdateServerRequest`: Validación para actualizar servidores

### Códigos de Estado HTTP
- **200 OK**: Operación exitosa (GET, PUT, DELETE)
- **201 Created**: Recurso creado exitosamente (POST)
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Error de validación

## 🚀 Tabla de Endpoints API

| Verbo HTTP | Ruta                  | Descripción                          | Código Éxito |
|------------|-----------------------|--------------------------------------|--------------|
| GET        | `/api/servers`        | Listar todos los servidores          | 200          |
| POST       | `/api/servers`        | Crear un nuevo servidor              | 201          |
| GET        | `/api/servers/{id}`   | Obtener un servidor específico       | 200          |
| PUT/PATCH  | `/api/servers/{id}`   | Actualizar un servidor existente     | 200          |
| DELETE     | `/api/servers/{id}`   | Eliminar un servidor                 | 200          |

**Códigos de Error:**
- `404` - Servidor no encontrado
- `422` - Error de validación

## 🐳 Despliegue con Docker

La configuración de infraestructura y despliegue se encuentra en un repositorio separado:

**🔗 https://github.com/sebainzulza/proyecto-infraestructura**

### ¿Por qué repositorios separados?

- **Separación de responsabilidades**: Código de aplicación vs configuración de infraestructura
- **Seguridad**: Variables sensibles y secretos no están en el código fuente
- **Despliegue independiente**: Cambios en infraestructura no afectan el versionado de la app
- **Control de acceso**: Diferentes permisos para desarrolladores y DevOps

### Contenido del Repositorio de Infraestructura

- `docker-compose.yml` - Orquestación de contenedores
- `docker/nginx/` - Configuración de Nginx
- `environments/` - Variables de entorno por ambiente
- `ansible/` - Playbooks de automatización (próximamente)
- `.github/workflows/` - Pipelines CI/CD (próximamente)

### Desarrollo Local con Docker

Este repositorio incluye un `Dockerfile` para construir la imagen de la aplicación.

Para desplegar localmente, clona el repositorio de infraestructura y sigue las instrucciones del README.

## 🔄 CI/CD Pipeline

El proyecto está preparado para integración continua con:
- **GitHub Actions** (pipeline en repositorio de infraestructura)
- **GitLab CI** (alternativa)
- Ejecución automática de:
  - ✅ Pruebas unitarias
  - ✅ Análisis estático (PHPStan)
  - ✅ Análisis de calidad (SonarQube)
  - ✅ Build de imagen Docker
  - ✅ Push a Container Registry
  - ✅ Despliegue automatizado con Ansible

## 📚 Documentación Adicional

- [API_EXAMPLES.md](API_EXAMPLES.md) - Ejemplos prácticos de uso de la API
- [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md) - Resumen técnico del proyecto
- [REPOSITORY_SEPARATION_GUIDE.md](REPOSITORY_SEPARATION_GUIDE.md) - Guía de separación de repositorios
- [Repositorio de Infraestructura](https://github.com/sebainzulza/proyecto-infraestructura) - Docker, CI/CD y despliegue

## 🎓 Proyecto Universitario

Este proyecto cumple con los siguientes requisitos académicos:

### Fase 1: Desarrollo y Estructura Base ✅
- [x] API RESTful funcional con operaciones CRUD
- [x] Patrón MVC correctamente aplicado
- [x] Código limpio y organizado
- [x] Pruebas unitarias/funcionales con PHPUnit
- [x] Análisis estático con PHPStan/Larastan
- [x] Integración con SonarQube
- [x] Documentación completa (README.md)

### Fase 2: CI/CD y Despliegue (En repositorio de infraestructura)
- [ ] Pipeline de CI/CD con GitHub Actions/GitLab CI
- [ ] Build automatizado de imágenes Docker
- [ ] Push a Container Registry
- [ ] Despliegue automatizado con Ansible
- [ ] Configuración de VM en cloud (AWS/Azure/GCP)

## 👥 Autor

**Sebastián Inzulza**
- GitHub: [@sebainzulza](https://github.com/sebainzulza)
- Proyecto: Desarrollo de API Laravel con CI/CD Automatizado
- Universidad: Proyecto Final

## 📄 Licencia

Este proyecto es parte de un trabajo universitario y está disponible bajo la licencia MIT.
