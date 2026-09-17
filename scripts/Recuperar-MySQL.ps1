[CmdletBinding()]
param(
    [ValidateSet('Menu', 'Diagnostico', 'Respaldo', 'RescatarSQL')]
    [string]$Accion = 'Menu',
    [string]$Xampp = 'C:\xampp',
    [string]$Destino = 'C:\Respaldos-MySQL',
    [ValidateRange(1024, 65535)][int]$PuertoRescate = 3307,
    [string]$Usuario = 'root',
    [switch]$PedirClave
)
$ErrorActionPreference = 'Stop'

function ComprobarDetenido {
    if (Get-Process -Name mysqld, mariadbd -ErrorAction SilentlyContinue) {
        throw 'MySQL esta ejecutandose. Detenlo desde XAMPP antes de respaldar o rescatar. No se forzara su cierre.'
    }
}

function CopiarVerificado([string]$Origen, [string]$Copia) {
    New-Item -ItemType Directory -Path $Copia -ErrorAction Stop | Out-Null
    $archivos = @(Get-ChildItem -LiteralPath $Origen -Recurse -Force -File)
    foreach ($carpeta in Get-ChildItem -LiteralPath $Origen -Recurse -Force -Directory) {
        $relativo = $carpeta.FullName.Substring($Origen.Length).TrimStart('\')
        New-Item -ItemType Directory -Path (Join-Path $Copia $relativo) -Force | Out-Null
    }
    foreach ($archivo in $archivos) {
        ComprobarDetenido
        $relativo = $archivo.FullName.Substring($Origen.Length).TrimStart('\')
        $nuevo = Join-Path $Copia $relativo
        Copy-Item -LiteralPath $archivo.FullName -Destination $nuevo
        if ((Get-FileHash -LiteralPath $archivo.FullName).Hash -ne (Get-FileHash -LiteralPath $nuevo).Hash) {
            throw "La verificacion del respaldo fallo: $relativo"
        }
    }
    ComprobarDetenido
}

try {
    $mysqlRoot = (Resolve-Path -LiteralPath (Join-Path $Xampp 'mysql')).Path
    $datos = (Resolve-Path -LiteralPath (Join-Path $mysqlRoot 'data')).Path
    $config = Join-Path $mysqlRoot 'bin\my.ini'
    $servidor = Join-Path $mysqlRoot 'bin\mysqld.exe'
    $dump = Join-Path $mysqlRoot 'bin\mysqldump.exe'
    $admin = Join-Path $mysqlRoot 'bin\mysqladmin.exe'
    foreach ($ruta in @($config, $servidor, $dump, $admin)) {
        if (!(Test-Path -LiteralPath $ruta -PathType Leaf)) { throw "No se encontro: $ruta" }
    }

    if ($Accion -eq 'Menu') {
        Write-Host "`nRECUPERAR MYSQL DE XAMPP"
        Write-Host '1. Diagnosticar (sin cambiar datos)'
        Write-Host '2. Respaldar todos los datos (MySQL detenido)'
        Write-Host '3. Rescatar SQL desde una copia (MySQL detenido)'
        Write-Host '0. Salir'
        switch (Read-Host 'Elige una opcion') {
            '1' { $Accion = 'Diagnostico' }
            '2' { $Accion = 'Respaldo' }
            '3' { $Accion = 'RescatarSQL' }
            default { exit 0 }
        }
    }

    if ($Accion -eq 'Diagnostico') {
        Write-Host "`nProcesos MySQL:"
        Get-Process -Name mysqld, mariadbd -ErrorAction SilentlyContinue | Select-Object Name, Id, Path | Format-Table
        Write-Host 'Puertos 3306 y de rescate:'
        Get-NetTCPConnection -State Listen -ErrorAction SilentlyContinue |
            Where-Object { $_.LocalPort -in @(3306, $PuertoRescate) } |
            Select-Object LocalAddress, LocalPort, OwningProcess | Format-Table
        Write-Host "`nUltimas entradas del registro (pueden incluir fallos anteriores):"
        $log = Join-Path $datos 'mysql_error.log'
        if (Test-Path -LiteralPath $log) { Get-Content -LiteralPath $log -Tail 50 }
        Write-Host "`nSi el puerto esta ocupado, identifica el proceso en XAMPP. Si hay errores InnoDB, usa RescatarSQL con MySQL detenido."
        exit 0
    }

    ComprobarDetenido
    # Esta herramienta solo opera con la distribucion estandar de XAMPP.
    $contenido = Get-Content -LiteralPath $config
    foreach ($linea in $contenido) {
        if ($linea -match '^\s*(datadir|innodb_data_home_dir|innodb_log_group_home_dir)\s*=\s*(.+?)\s*$') {
            $rutaConfigurada = $Matches[2].Trim('"', "'").Replace('/', '\').TrimEnd('\')
            if (![IO.Path]::IsPathRooted($rutaConfigurada) -or $rutaConfigurada -ine $datos.TrimEnd('\')) {
                throw 'La configuracion usa datos externos. Se necesita un respaldo adaptado a esa instalacion.'
            }
        }
        if ($linea -match '^\s*(innodb_force_recovery|innodb_undo_directory|innodb_directories)\s*=') {
            throw 'La configuracion contiene opciones de recuperacion o almacenamiento especiales. Revisar antes de continuar.'
        }
        if ($linea -match '^\s*innodb_data_file_path\s*=\s*(.+)$' -and $Matches[1].Trim() -ne 'ibdata1:10M:autoextend') {
            throw 'La configuracion InnoDB no es la estandar de XAMPP. Revisar antes de continuar.'
        }
    }
    if (Get-ChildItem -LiteralPath $datos -Recurse -Force | Where-Object {
        ($_.Attributes -band [IO.FileAttributes]::ReparsePoint) -or $_.Extension -eq '.isl'
    }) { throw 'Hay enlaces o tablas externas en data. No se puede garantizar un respaldo completo.' }
    $destinoAbsoluto = [IO.Path]::GetFullPath($Destino).TrimEnd('\')
    if ($destinoAbsoluto -ieq $mysqlRoot -or $destinoAbsoluto.StartsWith($mysqlRoot + '\', [StringComparison]::OrdinalIgnoreCase) -or
        $destinoAbsoluto.StartsWith(([IO.Path]::GetFullPath((Join-Path $Xampp 'htdocs'))).TrimEnd('\') + '\', [StringComparison]::OrdinalIgnoreCase) -or
        $destinoAbsoluto -ieq (Join-Path $Xampp 'htdocs')) {
        throw 'Elige un destino fuera de mysql y de htdocs para proteger el respaldo.'
    }
    $tamano = (Get-ChildItem -LiteralPath $datos -Recurse -Force -File | Measure-Object Length -Sum).Sum
    $unidad = Get-PSDrive -Name ([IO.Path]::GetPathRoot($destinoAbsoluto).TrimEnd('\').TrimEnd(':'))
    $necesario = $tamano * 4 + 1GB
    if ($unidad.Free -lt $necesario) { throw 'No hay espacio suficiente: se requiere margen para respaldo, copia de trabajo y SQL.' }
    $sesion = Join-Path $destinoAbsoluto ((Get-Date -Format 'yyyyMMdd_HHmmss') + '_' + [guid]::NewGuid().ToString('N').Substring(0,8))
    New-Item -ItemType Directory -Path $sesion -Force | Out-Null
    Copy-Item -LiteralPath $config -Destination (Join-Path $sesion 'my.ini')
    Write-Host "Respaldando y verificando los archivos en $sesion ..."
    CopiarVerificado $datos (Join-Path $sesion 'data-respaldo')
    'Copia fisica verificada. Puede contener la corrupcion del origen; no equivale a una base reparada.' |
        Set-Content -LiteralPath (Join-Path $sesion 'RESPALDO-COMPLETO.txt')
    if ($Accion -eq 'Respaldo') { Write-Host "Respaldo terminado: $sesion"; exit 0 }

    if (Get-NetTCPConnection -State Listen -LocalPort $PuertoRescate -ErrorAction SilentlyContinue) {
        throw "El puerto de rescate $PuertoRescate esta ocupado. El respaldo se conserva en $sesion"
    }
    $trabajo = Join-Path $sesion 'data-trabajo'
    CopiarVerificado (Join-Path $sesion 'data-respaldo') $trabajo
    $logRescate = Join-Path $sesion 'rescate.log'
    $proceso = $null
    $cliente = @('--no-defaults', '--protocol=tcp', '--host=127.0.0.1', "--port=$PuertoRescate", "--user=$Usuario")
    if ($PedirClave) { $cliente += '--password' }
    try {
        # --no-defaults evita que el proceso auxiliar abra data de produccion.
        $argumentos = @('--no-defaults', ('--basedir="' + $mysqlRoot + '"'), ('--datadir="' + $trabajo + '"'),
            "--port=$PuertoRescate", '--bind-address=127.0.0.1', '--innodb-force-recovery=1',
            '--read-only=ON', '--event-scheduler=OFF', '--skip-slave-start', '--skip-log-bin',
            ('--pid-file="' + (Join-Path $sesion 'rescate.pid') + '"'), ('--log-error="' + $logRescate + '"'))
        $proceso = Start-Process -FilePath $servidor -ArgumentList $argumentos -WorkingDirectory $trabajo -WindowStyle Hidden -PassThru
        $listo = $false
        for ($i = 0; $i -lt 30; $i++) {
            if ($proceso.HasExited) { break }
            $escucha = Get-NetTCPConnection -State Listen -LocalPort $PuertoRescate -ErrorAction SilentlyContinue |
                Where-Object { $_.OwningProcess -eq $proceso.Id }
            if ($escucha) { $listo = $true; break }
            Start-Sleep -Seconds 1
        }
        if (!$listo) { throw "La copia no pudo iniciar con recuperacion nivel 1. Consulta $logRescate. No se aumentara el nivel automaticamente." }
        $parcial = Join-Path $sesion 'rescate-incompleto.sql'
        & $dump @cliente '--all-databases' '--routines' '--events' '--triggers' '--hex-blob' '--quick' '--skip-lock-tables' "--result-file=$parcial"
        if ($LASTEXITCODE -ne 0 -or !(Test-Path -LiteralPath $parcial) -or (Get-Item -LiteralPath $parcial).Length -eq 0) {
            throw "La exportacion fallo. El SQL incompleto no debe restaurarse como respaldo completo. Revisa $sesion"
        }
        $sqlFinal = Join-Path $sesion 'bases-recuperadas.sql'
        Move-Item -LiteralPath $parcial -Destination $sqlFinal
        Write-Host "SQL extraido: $sqlFinal"
        Write-Host 'Importalo y comprueba las tablas en una instancia limpia compatible antes de reemplazar la original.'
    } finally {
        if ($proceso -and !$proceso.HasExited) {
            & $admin @cliente 'shutdown'
            if (!$proceso.WaitForExit(10000)) {
                # Solo el proceso auxiliar creado aqui, nunca la instancia original.
                Stop-Process -InputObject $proceso -ErrorAction SilentlyContinue
            }
        }
    }
} catch {
    Write-Host ("ERROR: " + $_.Exception.Message) -ForegroundColor Red
    exit 1
}
