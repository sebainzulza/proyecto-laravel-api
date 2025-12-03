# Script de Prueba Rápida de la API
# Este script prueba los endpoints principales de la API

Write-Host "`n=== PRUEBA DE API DE SERVIDORES ===" -ForegroundColor Cyan
Write-Host "Asegúrate de tener el servidor corriendo: php artisan serve`n" -ForegroundColor Yellow

$baseUrl = "http://localhost:8000/api/servers"
$headers = @{
    "Accept" = "application/json"
    "Content-Type" = "application/json"
}

# 1. Listar servidores (debe estar vacío inicialmente)
Write-Host "1. Listando servidores..." -ForegroundColor Green
try {
    $response = Invoke-RestMethod -Uri $baseUrl -Method GET -Headers $headers
    Write-Host "✓ Respuesta recibida: $($response.data.Count) servidores encontrados" -ForegroundColor Green
} catch {
    Write-Host "✗ Error al listar servidores" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

# 2. Crear un servidor
Write-Host "`n2. Creando un servidor..." -ForegroundColor Green
$newServer = @{
    name = "Test Server $(Get-Random -Minimum 1 -Maximum 1000)"
    ip_address = "192.168.1.$(Get-Random -Minimum 1 -Maximum 254)"
    status = $true
} | ConvertTo-Json

try {
    $created = Invoke-RestMethod -Uri $baseUrl -Method POST -Headers $headers -Body $newServer
    $serverId = $created.data.id
    Write-Host "✓ Servidor creado con ID: $serverId" -ForegroundColor Green
    Write-Host "  Nombre: $($created.data.name)" -ForegroundColor White
    Write-Host "  IP: $($created.data.ip_address)" -ForegroundColor White
} catch {
    Write-Host "✗ Error al crear servidor" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit
}

# 3. Obtener el servidor creado
Write-Host "`n3. Obteniendo el servidor creado..." -ForegroundColor Green
try {
    $server = Invoke-RestMethod -Uri "$baseUrl/$serverId" -Method GET -Headers $headers
    Write-Host "✓ Servidor obtenido: $($server.data.name)" -ForegroundColor Green
} catch {
    Write-Host "✗ Error al obtener servidor" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

# 4. Actualizar el servidor
Write-Host "`n4. Actualizando el servidor..." -ForegroundColor Green
$updateData = @{
    name = "Updated Server $(Get-Random -Minimum 1 -Maximum 1000)"
    ip_address = "10.0.0.$(Get-Random -Minimum 1 -Maximum 254)"
    status = $false
} | ConvertTo-Json

try {
    $updated = Invoke-RestMethod -Uri "$baseUrl/$serverId" -Method PUT -Headers $headers -Body $updateData
    Write-Host "✓ Servidor actualizado" -ForegroundColor Green
    Write-Host "  Nuevo nombre: $($updated.data.name)" -ForegroundColor White
    Write-Host "  Nueva IP: $($updated.data.ip_address)" -ForegroundColor White
    Write-Host "  Estado: $($updated.data.status)" -ForegroundColor White
} catch {
    Write-Host "✗ Error al actualizar servidor" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

# 5. Listar servidores (debe mostrar el servidor creado)
Write-Host "`n5. Listando servidores nuevamente..." -ForegroundColor Green
try {
    $response = Invoke-RestMethod -Uri $baseUrl -Method GET -Headers $headers
    Write-Host "✓ Total de servidores: $($response.data.Count)" -ForegroundColor Green
    Write-Host "`nServidores en la base de datos:" -ForegroundColor Cyan
    foreach ($srv in $response.data) {
        Write-Host "  - ID: $($srv.id) | Nombre: $($srv.name) | IP: $($srv.ip_address) | Estado: $($srv.status)" -ForegroundColor White
    }
} catch {
    Write-Host "✗ Error al listar servidores" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

Write-Host "`n=== PRUEBAS COMPLETADAS ===" -ForegroundColor Cyan
Write-Host "Servidor(es) creado(s) y disponible(s) en http://127.0.0.1:8000/api/servers`n" -ForegroundColor Green
