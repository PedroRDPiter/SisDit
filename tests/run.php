<?php

require_once dirname(__DIR__) . '/php/Utilidades.php';

$pruebas = [];
$fallos = [];

function prueba(string $nombre, callable $ejecutar): void
{
    global $pruebas, $fallos;
    try {
        $ejecutar();
        $pruebas[] = $nombre;
        echo "[OK] {$nombre}\n";
    } catch (Throwable $error) {
        $fallos[] = $nombre . ': ' . $error->getMessage();
        echo "[FALLO] {$nombre}: {$error->getMessage()}\n";
    }
}

function afirmar($condicion, string $mensaje = 'La condición esperada no se cumplió'): void
{
    if (!$condicion) throw new RuntimeException($mensaje);
}

function afirmarIgual($esperado, $actual, string $mensaje = ''): void
{
    if ($esperado !== $actual) {
        throw new RuntimeException($mensaje !== '' ? $mensaje : 'Esperado ' . var_export($esperado, true) . ', recibido ' . var_export($actual, true));
    }
}

function afirmarLanza(string $clase, callable $ejecutar): void
{
    try {
        $ejecutar();
    } catch (Throwable $error) {
        afirmar($error instanceof $clase, 'Se lanzó ' . get_class($error) . ' en lugar de ' . $clase);
        return;
    }
    throw new RuntimeException('No se lanzó la excepción esperada ' . $clase);
}

prueba('normaliza estados con acentos y mayúsculas', function (): void {
    afirmarIgual('en revision', Utilidades::normalizarEstatus('  En Revisión '));
});

prueba('identifica únicamente estados visibles para ventanilla', function (): void {
    afirmar(Utilidades::esEstatusAprobadoParaVentanilla('Pendiente por firmar'));
    afirmar(Utilidades::esEstatusAprobadoParaVentanilla('Firmado'));
    afirmar(!Utilidades::esEstatusAprobadoParaVentanilla('En revisión'));
    afirmar(!Utilidades::esEstatusAprobadoParaVentanilla('En corrección'));
});

prueba('agrupa todos los trámites de una cuenta catastral', function (): void {
    $grupos = Utilidades::agruparTramitesPorCuenta([
        ['id' => 1, 'cuenta_catastral' => ' 0100 '],
        ['id' => 2, 'cuenta_catastral' => '0100'],
        ['id' => 3, 'cuenta_catastral' => '0200'],
        ['id' => 4, 'cuenta_catastral' => ''],
    ]);
    afirmarIgual(2, count($grupos['0100']));
    afirmarIgual(1, count($grupos['0200']));
    afirmarIgual(2, count($grupos));
});

prueba('rechaza archivos que exceden el tamaño máximo', function (): void {
    $temporal = tempnam(sys_get_temp_dir(), 'sisdit_test_');
    file_put_contents($temporal, '%PDF-1.4');
    try {
        afirmarLanza(ArchivoException::class, function () use ($temporal): void {
            Utilidades::validarArchivo([
                'name' => 'documento.pdf', 'tmp_name' => $temporal,
                'size' => 11, 'error' => UPLOAD_ERR_OK,
            ], ['pdf'], 10);
        });
    } finally {
        @unlink($temporal);
    }
});

prueba('rechaza contenido cuyo MIME no coincide con la extensión', function (): void {
    $temporal = tempnam(sys_get_temp_dir(), 'sisdit_test_');
    file_put_contents($temporal, 'contenido de texto');
    try {
        afirmarLanza(ArchivoException::class, function () use ($temporal): void {
            Utilidades::validarArchivo([
                'name' => 'documento.pdf', 'tmp_name' => $temporal,
                'size' => filesize($temporal), 'error' => UPLOAD_ERR_OK,
            ], ['pdf']);
        });
    } finally {
        @unlink($temporal);
    }
});

prueba('acepta un PDF dentro del límite permitido', function (): void {
    $temporal = tempnam(sys_get_temp_dir(), 'sisdit_test_');
    file_put_contents($temporal, "%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF");
    try {
        $resultado = Utilidades::validarArchivo([
            'name' => 'documento.PDF', 'tmp_name' => $temporal,
            'size' => filesize($temporal), 'error' => UPLOAD_ERR_OK,
        ], ['pdf']);
        afirmarIgual('pdf', $resultado['extension']);
        afirmarIgual('application/pdf', $resultado['mime']);
    } finally {
        @unlink($temporal);
    }
});

echo "\n" . count($pruebas) . ' prueba(s) correctas; ' . count($fallos) . " fallo(s).\n";
exit($fallos === [] ? 0 : 1);
