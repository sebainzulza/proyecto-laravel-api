# Guía de Despliegue con Docker

Esta guía explica cómo construir y ejecutar la API Laravel usando Docker y Docker Compose.

## 📋 Prerrequisitos

- Docker 20.10 o superior
- Docker Compose 2.0 o superior

## 🚀 Inicio Rápido

### 1. Preparar el archivo de entorno

```bash
# Copiar el archivo de entorno para Docker
cp .env.docker .env

# Generar la clave de aplicación (desde el host)
php artisan key:generate
```

### 2. Construir y levantar los contenedores

```bash
# Construir las imágenes
docker-compose build

# Levantar los servicios
docker-compose up -d

# Ver los logs
docker-compose logs -f
```

### 3. Inicializar la base de datos

```bash
# Ejecutar migraciones
docker-compose exec app php artisan migrate --force

# (Opcional) Crear datos de prueba
docker-compose exec app php artisan db:seed
```

### 4. Verificar la instalación

La API estará disponible en: **http://localhost:8080/api/servers**

```bash
# Probar la API
curl http://localhost:8080/api/servers
```

## 🔧 Comandos Útiles

### Gestión de contenedores

```bash
# Ver estado de los servicios
docker-compose ps

# Detener los servicios
docker-compose stop

# Iniciar los servicios detenidos
docker-compose start

# Reiniciar los servicios
docker-compose restart

# Detener y eliminar contenedores
docker-compose down

# Detener y eliminar contenedores + volúmenes
docker-compose down -v
```

### Ejecutar comandos Artisan

```bash
# Limpiar cache
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

# Ver rutas
docker-compose exec app php artisan route:list

# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Refrescar base de datos
docker-compose exec app php artisan migrate:fresh
```

### Acceder al contenedor

```bash
# Shell en el contenedor de la aplicación
docker-compose exec app bash

# Shell en el contenedor de Nginx
docker-compose exec nginx sh

# Shell en el contenedor de MySQL
docker-compose exec db bash
```

### Ver logs

```bash
# Logs de todos los servicios
docker-compose logs -f

# Logs de un servicio específico
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
```

## 🔒 Producción en AWS

### 1. Preparar para AWS

```bash
# Construir imagen para producción
docker build -t laravel-api:production .

# Etiquetar para ECR (Amazon Elastic Container Registry)
docker tag laravel-api:production <account-id>.dkr.ecr.<region>.amazonaws.com/laravel-api:latest

# Push a ECR
docker push <account-id>.dkr.ecr.<region>.amazonaws.com/laravel-api:latest
```

### 2. Variables de entorno en AWS

Configurar las siguientes variables en ECS/Elastic Beanstalk/EC2:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... # Generar con php artisan key:generate
DB_HOST=<rds-endpoint>
DB_DATABASE=servers_api
DB_USERNAME=<db-user>
DB_PASSWORD=<db-password>
```

### 3. Configuración de red

- **Puerto de la aplicación**: 8080 (Nginx)
- **Health check**: `GET /api/servers`
- **Timeout**: 30 segundos

## 🐛 Troubleshooting

### Error: Permission denied en storage/logs

```bash
# Arreglar permisos dentro del contenedor
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 775 /var/www/storage
```

### Error: Base de datos no conecta

```bash
# Verificar que el contenedor de MySQL esté corriendo
docker-compose ps db

# Verificar logs de MySQL
docker-compose logs db

# Esperar a que MySQL esté listo (puede tomar 30-60 segundos)
docker-compose exec db mysqladmin ping -h localhost
```

### Error: 502 Bad Gateway

```bash
# Verificar que PHP-FPM esté corriendo
docker-compose ps app

# Ver logs del contenedor de la aplicación
docker-compose logs app

# Verificar configuración de Nginx
docker-compose exec nginx nginx -t
```

### Reconstruir desde cero

```bash
# Eliminar todo y reconstruir
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

## 📊 Monitoreo

### Verificar uso de recursos

```bash
# Ver estadísticas de contenedores
docker stats

# Ver uso de disco
docker system df
```

### Health checks

Añadir a `docker-compose.yml`:

```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "http://localhost/api/servers"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 40s
```

## 🔐 Seguridad

### Recomendaciones para producción:

1. **NO usar credenciales por defecto**: Cambiar `MYSQL_ROOT_PASSWORD`, `MYSQL_PASSWORD`
2. **Usar secrets de Docker**: Para credenciales sensibles
3. **HTTPS**: Usar un proxy reverso (ALB en AWS) con certificado SSL
4. **Firewall**: Restringir acceso a puertos innecesarios
5. **Actualizaciones**: Mantener imágenes actualizadas

## 📦 Optimización

### Reducir tamaño de imagen

El Dockerfile ya está optimizado con:
- ✅ Multi-stage no necesario (imagen base pequeña)
- ✅ Limpieza de apt cache
- ✅ Composer con `--no-dev`
- ✅ Autoloader optimizado

### Mejorar rendimiento

```yaml
# En docker-compose.yml, añadir para PHP-FPM:
environment:
  - PHP_FPM_PM=dynamic
  - PHP_FPM_PM_MAX_CHILDREN=20
  - PHP_FPM_PM_START_SERVERS=2
  - PHP_FPM_PM_MIN_SPARE_SERVERS=1
  - PHP_FPM_PM_MAX_SPARE_SERVERS=3
```

## 📝 Estructura de Archivos Docker

```
api-servidores/
├── Dockerfile              # Imagen PHP-FPM optimizada
├── .dockerignore          # Archivos excluidos del build
├── docker-compose.yml     # Orquestación de servicios
├── .env.docker           # Variables de entorno para Docker
└── docker/
    └── nginx/
        └── nginx.conf     # Configuración de Nginx
```
