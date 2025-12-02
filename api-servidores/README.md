# API de Inventario de Servidores

API RESTful desarrollada con Laravel para gestionar un inventario de servidores. Este proyecto implementa operaciones CRUD completas con validación robusta, tipado estricto y pruebas automatizadas.

## 📋 Características

- ✅ API RESTful con operaciones CRUD completas
- ✅ Validación de datos con Form Requests separados
- ✅ Tipado estricto en todo el código (strict_types=1)
- ✅ Pruebas automatizadas con PHPUnit (100% cobertura de endpoints)
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
# Clonar el repositorio
git clone https://github.com/sebainzulza/proyecto-laravel-api.git
cd proyecto-laravel-api/api-servidores

# Instalar dependencias
composer install --ignore-platform-req=ext-fileinfo

# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate

# Ejecutar migraciones
php artisan migrate

# Iniciar servidor de desarrollo
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

## 🧪 Pruebas

El proyecto incluye pruebas automatizadas completas que cubren:
- ✅ Listar servidores
- ✅ Crear servidor exitosamente
- ✅ Validación al crear (nombre requerido, IP válida, nombre único)
- ✅ Obtener servidor específico
- ✅ Error 404 al buscar servidor inexistente
- ✅ Actualizar servidor
- ✅ Eliminar servidor
- ✅ Error 404 al eliminar servidor inexistente

### Ejecutar las Pruebas

```bash
# Ejecutar todas las pruebas
php artisan test

# Ejecutar solo las pruebas de la API de servidores
php artisan test --filter ServerApiTest

# Ejecutar con cobertura
php artisan test --coverage
```

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

## 🔜 Próximos Pasos

Para el proyecto completo se implementará:
- [ ] Análisis estático con PHPStan/Larastan
- [ ] Integración con SonarQube
- [ ] Pipeline CI/CD con GitHub Actions
- [ ] Dockerización de la aplicación
- [ ] Despliegue automatizado con Ansible

## 👥 Autor

**Sebastián Inzulza**
- GitHub: [@sebainzulza](https://github.com/sebainzulza)

## 📄 Licencia

Este proyecto es parte de un trabajo universitario y está disponible bajo la licencia MIT.
