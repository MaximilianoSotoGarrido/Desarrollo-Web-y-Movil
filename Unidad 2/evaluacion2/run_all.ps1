# Levanta todos los servicios de OfiExpress en ventanas separadas (Windows PowerShell).
# Uso (desde la carpeta evaluacion2):
#     powershell -ExecutionPolicy Bypass -File .\run_all.ps1
# Requisitos previos: MongoDB corriendo, 'pip install -r requirements.txt'
# y 'npm.cmd install' dentro de graphql-gateway.

$base = $PSScriptRoot

function Start-Servicio($titulo, $carpeta, $comando) {
    $ruta = Join-Path $base $carpeta
    $arranque = "Set-Location '$ruta'; `$host.UI.RawUI.WindowTitle='$titulo'; $comando"
    Start-Process powershell -ArgumentList "-NoExit", "-ExecutionPolicy", "Bypass", "-Command", $arranque
    Start-Sleep -Milliseconds 800
}

Start-Servicio "secret-manager" "secret-manager"  "python -m uvicorn secret_manager:app --host 127.0.0.1 --port 8200"
Start-Servicio "auth-service"   "auth-service"    "python -m uvicorn auth_service:app --host 127.0.0.1 --port 8100"
Start-Servicio "inventory"      "inventory"       "python -m uvicorn inventory:app --host 127.0.0.1 --port 9000"
Start-Servicio "orders"         "orders"          "python -m uvicorn orders:app --host 127.0.0.1 --port 9100"
Start-Servicio "clients"        "clients"         "python -m uvicorn clients:app --host 127.0.0.1 --port 9200"
Start-Servicio "payments"       "payments"        "python -m uvicorn payments:app --host 127.0.0.1 --port 9300"
Start-Servicio "graphql"        "graphql-gateway" "node server.js"
Start-Sleep -Seconds 2
Start-Servicio "API-GATEWAY"    "gateway"         "python -m uvicorn gateway:app --host 0.0.0.0 --port 8000"

Write-Host ""
Write-Host "Servicios iniciados en ventanas separadas." -ForegroundColor Green
Write-Host "Ahora, en esta ventana:  python seed.py   y luego   python tests\pruebas.py" -ForegroundColor Yellow
