# Ejemplos de Uso de la API

Este documento contiene ejemplos prácticos para probar la API de Inventario de Servidores usando diferentes herramientas.

## 🚀 Inicio Rápido

Primero, asegúrate de que el servidor esté corriendo:

```bash
php artisan serve
```

La API estará disponible en: `http://localhost:8000/api`

## 📡 Ejemplos con cURL

### 1. Listar todos los servidores

```bash
curl -X GET http://localhost:8000/api/servers \
  -H "Accept: application/json"
```

### 2. Crear un nuevo servidor

```bash
curl -X POST http://localhost:8000/api/servers \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Production Server",
    "ip_address": "192.168.1.100",
    "status": true
  }'
```

### 3. Obtener un servidor específico

```bash
curl -X GET http://localhost:8000/api/servers/1 \
  -H "Accept: application/json"
```

### 4. Actualizar un servidor

```bash
curl -X PUT http://localhost:8000/api/servers/1 \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Updated Production Server",
    "ip_address": "192.168.1.150",
    "status": false
  }'
```

### 5. Eliminar un servidor

```bash
curl -X DELETE http://localhost:8000/api/servers/1 \
  -H "Accept: application/json"
```

## 🔧 Ejemplos con PowerShell

### 1. Listar todos los servidores

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/servers" `
  -Method GET `
  -Headers @{"Accept"="application/json"}
```

### 2. Crear un nuevo servidor

```powershell
$body = @{
    name = "Production Server"
    ip_address = "192.168.1.100"
    status = $true
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/servers" `
  -Method POST `
  -Headers @{"Accept"="application/json"; "Content-Type"="application/json"} `
  -Body $body
```

### 3. Obtener un servidor específico

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/servers/1" `
  -Method GET `
  -Headers @{"Accept"="application/json"}
```

### 4. Actualizar un servidor

```powershell
$body = @{
    name = "Updated Production Server"
    ip_address = "192.168.1.150"
    status = $false
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://localhost:8000/api/servers/1" `
  -Method PUT `
  -Headers @{"Accept"="application/json"; "Content-Type"="application/json"} `
  -Body $body
```

### 5. Eliminar un servidor

```powershell
Invoke-RestMethod -Uri "http://localhost:8000/api/servers/1" `
  -Method DELETE `
  -Headers @{"Accept"="application/json"}
```

## 🧪 Ejemplos de Pruebas de Validación

### Error: Crear servidor sin nombre

```bash
curl -X POST http://localhost:8000/api/servers \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "ip_address": "192.168.1.100"
  }'
```

**Respuesta esperada (422)**:
```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

### Error: IP inválida

```bash
curl -X POST http://localhost:8000/api/servers \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Test Server",
    "ip_address": "invalid-ip"
  }'
```

**Respuesta esperada (422)**:
```json
{
  "message": "The ip address field must be a valid IP address.",
  "errors": {
    "ip_address": ["The ip address field must be a valid IP address."]
  }
}
```

### Error: Nombre duplicado

Primero crea un servidor, luego intenta crear otro con el mismo nombre:

```bash
# Primer servidor
curl -X POST http://localhost:8000/api/servers \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Unique Server",
    "ip_address": "192.168.1.100"
  }'

# Intento de duplicar (fallará)
curl -X POST http://localhost:8000/api/servers \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Unique Server",
    "ip_address": "192.168.1.200"
  }'
```

**Respuesta esperada (422)**:
```json
{
  "message": "The name has already been taken.",
  "errors": {
    "name": ["The name has already been taken."]
  }
}
```

## 📱 Usando Postman

### Importar Colección

Puedes crear una colección en Postman con los siguientes endpoints:

1. **GET** `/api/servers` - List Servers
2. **POST** `/api/servers` - Create Server
3. **GET** `/api/servers/:id` - Show Server
4. **PUT** `/api/servers/:id` - Update Server
5. **DELETE** `/api/servers/:id` - Delete Server

### Variables de Entorno en Postman

```
base_url = http://localhost:8000/api
```

### Headers Requeridos

```
Accept: application/json
Content-Type: application/json
```

## 🐛 Troubleshooting

### El servidor no responde

```bash
# Verificar que el servidor esté corriendo
php artisan serve

# Verificar la configuración
php artisan config:clear
php artisan cache:clear
```

### Errores de base de datos

```bash
# Ejecutar migraciones
php artisan migrate

# Si necesitas reiniciar la base de datos
php artisan migrate:fresh
```

### Ver logs de errores

```bash
# Ver los últimos logs
tail -f storage/logs/laravel.log

# En Windows PowerShell
Get-Content storage/logs/laravel.log -Tail 50 -Wait
```

## 📚 Recursos Adicionales

- [Documentación de Laravel](https://laravel.com/docs)
- [HTTP Status Codes](https://httpstatuses.com/)
- [JSON API Specification](https://jsonapi.org/)
