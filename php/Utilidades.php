<?php

class ValidacionException extends RuntimeException {}
class ArchivoException extends ValidacionException {}

final class Utilidades
{
    public const TAMANO_MAXIMO_ARCHIVO = 10485760; // 10 MiB

    private const MIMES_POR_EXTENSION = [
        'pdf' => ['application/pdf'],
        'jpg' => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'csv' => ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel'],
    ];

    private const ESTATUS_APROBADOS_VENTANILLA = [
        'aprobado', 'pendiente por firmar', 'firmado', 'entregado y archivado',
    ];

    public static function validarArchivo(
        array $archivo,
        array $extensionesPermitidas = ['pdf', 'jpg', 'jpeg', 'png'],
        int $tamanoMaximo = self::TAMANO_MAXIMO_ARCHIVO
    ): array {
        $error = isset($archivo['error']) ? (int) $archivo['error'] : UPLOAD_ERR_NO_FILE;
        if ($error !== UPLOAD_ERR_OK) {
            $mensajes = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el límite configurado en el servidor.',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el límite permitido por el formulario.',
                UPLOAD_ERR_PARTIAL => 'El archivo se recibió parcialmente.',
                UPLOAD_ERR_NO_FILE => 'No se seleccionó ningún archivo.',
                UPLOAD_ERR_NO_TMP_DIR => 'No hay carpeta temporal disponible.',
                UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en disco.',
                UPLOAD_ERR_EXTENSION => 'Una extensión del servidor bloqueó la carga.',
            ];
            throw new ArchivoException($mensajes[$error] ?? 'Error desconocido al recibir el archivo.');
        }

        $tamano = (int) ($archivo['size'] ?? 0);
        if ($tamano <= 0) throw new ArchivoException('El archivo está vacío.');
        if ($tamano > $tamanoMaximo) {
            throw new ArchivoException('El archivo excede el tamaño máximo de ' . self::formatearBytes($tamanoMaximo) . '.');
        }

        $extension = strtolower(pathinfo((string) ($archivo['name'] ?? ''), PATHINFO_EXTENSION));
        $permitidas = array_values(array_unique(array_map('strtolower', $extensionesPermitidas)));
        if ($extension === '' || !in_array($extension, $permitidas, true) || !isset(self::MIMES_POR_EXTENSION[$extension])) {
            throw new ArchivoException('Tipo de archivo no permitido. Formatos válidos: ' . implode(', ', $permitidas) . '.');
        }

        $temporal = (string) ($archivo['tmp_name'] ?? '');
        if ($temporal === '' || !is_file($temporal) || !is_readable($temporal)) {
            throw new ArchivoException('El archivo temporal no está disponible.');
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) throw new ArchivoException('No fue posible verificar el tipo MIME del archivo.');
        try {
            $mime = (string) finfo_file($finfo, $temporal);
        } finally {
            unset($finfo);
        }
        if (!in_array($mime, self::MIMES_POR_EXTENSION[$extension], true)) {
            throw new ArchivoException('El contenido del archivo no corresponde con la extensión .' . $extension . '.');
        }
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) && @getimagesize($temporal) === false) {
            throw new ArchivoException('El archivo no contiene una imagen válida.');
        }
        return ['extension' => $extension, 'mime' => $mime, 'tamano' => $tamano];
    }

    public static function crearDirectorioSeguro(string $ruta): void
    {
        $ruta = rtrim($ruta, '/\\') . DIRECTORY_SEPARATOR;
        if (!is_dir($ruta) && !mkdir($ruta, 0755, true) && !is_dir($ruta)) {
            throw new ArchivoException('No fue posible crear el directorio de archivos.');
        }
        if (!is_writable($ruta)) throw new ArchivoException('El directorio de archivos no tiene permisos de escritura.');
        $proteccion = $ruta . '.htaccess';
        if (!is_file($proteccion)) {
            $contenido = "Options -Indexes\nAddType application/octet-stream .php .phtml .php3 .php4 .php5\nphp_flag engine off\n";
            if (file_put_contents($proteccion, $contenido, LOCK_EX) === false) {
                throw new ArchivoException('No fue posible proteger el directorio de archivos.');
            }
        }
    }

    public static function generarNombreArchivo(string $prefijo, string $extension): string
    {
        $prefijo = preg_replace('/[^a-zA-Z0-9_-]/', '_', $prefijo) ?: 'archivo';
        return $prefijo . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . strtolower($extension);
    }

    public static function normalizarCuentaCatastral(mixed $cuenta): string
    {
        return strtoupper(trim((string) $cuenta));
    }

    public static function normalizarEstatus(mixed $estatus): string
    {
        $valor = trim(mb_strtolower((string) $estatus, 'UTF-8'));
        $valor = strtr($valor, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $valor);
        $normalizado = $ascii === false ? $valor : strtolower($ascii);
        $normalizado = preg_replace('/[^a-z0-9]+/', ' ', $normalizado);
        return trim(preg_replace('/\s+/', ' ', (string) $normalizado));
    }

    public static function esEstatusAprobadoParaVentanilla(mixed $estatus): bool
    {
        return in_array(self::normalizarEstatus($estatus), self::ESTATUS_APROBADOS_VENTANILLA, true);
    }

    public static function agruparTramitesPorCuenta(array $tramites): array
    {
        $grupos = [];
        foreach ($tramites as $tramite) {
            if (!is_array($tramite)) continue;
            $cuenta = self::normalizarCuentaCatastral($tramite['cuenta_catastral'] ?? '');
            if ($cuenta !== '') $grupos[$cuenta][] = $tramite;
        }
        return $grupos;
    }

    private static function formatearBytes(int $bytes): string
    {
        return $bytes % 1048576 === 0 ? ($bytes / 1048576) . ' MB' : number_format($bytes / 1048576, 1) . ' MB';
    }
}
