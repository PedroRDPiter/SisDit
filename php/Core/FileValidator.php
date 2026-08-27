<?php
declare(strict_types=1);

require_once __DIR__ . '/ApiException.php';

final class FileValidator
{
    private const MIME_BY_EXTENSION = [
        'pdf' => ['application/pdf'],
        'jpg' => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png' => ['image/png'],
        'webp' => ['image/webp'],
    ];

    public static function validate(array $file, array $extensions = ['pdf', 'jpg', 'jpeg', 'png'], int $maxBytes = 5242880): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || !is_uploaded_file((string)($file['tmp_name'] ?? ''))) {
            throw new ApiException('No se recibió un archivo válido', 422);
        }
        $size = (int)($file['size'] ?? 0);
        if ($size < 1 || $size > $maxBytes) throw new ApiException('El archivo excede el tamaño máximo permitido', 422);
        $extension = strtolower(pathinfo((string)($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($extension, $extensions, true) || !isset(self::MIME_BY_EXTENSION[$extension])) {
            throw new ApiException('Tipo de archivo no permitido', 422);
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file((string)$file['tmp_name']);
        if (!in_array($mime, self::MIME_BY_EXTENSION[$extension], true)) {
            throw new ApiException('El contenido del archivo no coincide con su extensión', 422);
        }
        if (str_starts_with($mime, 'image/') && @getimagesize((string)$file['tmp_name']) === false) {
            throw new ApiException('La imagen está dañada o no es válida', 422);
        }
        return ['extension' => $extension, 'mime' => $mime, 'size' => $size, 'sha256' => hash_file('sha256', (string)$file['tmp_name'])];
    }
}
