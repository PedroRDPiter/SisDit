# Pruebas

Ejecutar desde la raíz del proyecto:

```powershell
C:\xampp\php\php.exe tests\run.php
```

Las pruebas no requieren base de datos ni dependencias externas. Cubren validación MIME/tamaño, normalización de estados, visibilidad de polígonos aprobados y agrupación de trámites por cuenta catastral.

También comprueban permisos y transiciones de firma/cierre y las rutas protegidas de documentos.

## Firma de PDF y cierre en navegador

Requiere Chrome y Playwright. Se utiliza una base **nueva y aislada**; el preparador copia solo las estructuras de `sistema` y crea datos ficticios, sin copiar personas ni expedientes reales. El puerto 8097 debe estar reservado para este servidor de pruebas.

```powershell
$env:SISDIT_DB_NAME = 'sisdit_test_oficios_local'
C:\xampp\php\php.exe tests\fixture_oficios.php
C:\xampp\php\php.exe -S 127.0.0.1:8097 -t .
```

En otra terminal, desde la raíz del proyecto:

```powershell
$herramientasPrueba = Join-Path $env:TEMP 'sisdit-browser-tools'
npm install --prefix $herramientasPrueba playwright --no-audit --no-fund
$env:SISDIT_PLAYWRIGHT = Join-Path $herramientasPrueba 'node_modules/playwright'
node tests/admin-oficios.browser.cjs
```

Al terminar, detener el servidor y eliminar únicamente la base y documentos ficticios de esa ejecución:

```powershell
$env:SISDIT_DB_NAME = 'sisdit_test_oficios_local'
C:\xampp\php\php.exe tests\fixture_oficios.php limpiar
```

La prueba guarda capturas y un PDF ficticio en `%TEMP%\sisdit-oficios-tests`. Verifica la firma dibujada y la carga PNG, la posición visible sobre una página rotada, la conservación del original, el cierre con el PDF guardado, la auditoría, el aislamiento entre subtrámites, CSRF, permisos y rechazos de archivos o estados incorrectos.
