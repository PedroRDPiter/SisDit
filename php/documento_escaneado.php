<?php

/** Localiza la constancia o licencia escaneada que se muestra desde el mapa. */
function obtenerDocumentoEscaneadoTramite(array $tramite): array
{
    // Las licencias usan tipos de archivo distintos a las constancias.
    $esLicencia = (int)($tramite['tipo_tramite_id'] ?? 0) === 7;
    $tiposAceptados = $esLicencia
        ? ['licencia_de_construccion', 'licencia_construccion', 'licencia']
        : ['formato', 'formato_constancia', 'constancia'];

    $archivo = '';
    // Busca primero en los archivos adicionales, conservando el más reciente.
    $otros = json_decode((string)($tramite['otros_archivos'] ?? ''), true);
    if (is_array($otros)) {
        // El ultimo archivo del mismo tipo es el reemplazo mas reciente.
        foreach (array_reverse($otros) as $documento) {
            if (!is_array($documento)) continue;
            // Se aceptan variantes del tipo y coincidencias en la etiqueta.
            $tipo = strtolower(trim((string)($documento['tipo'] ?? '')));
            $etiqueta = strtolower(trim((string)($documento['label'] ?? '')));
            $coincideEtiqueta = $esLicencia
                ? strpos($etiqueta, 'licencia') !== false
                : strpos($etiqueta, 'constancia') !== false;
            if (in_array($tipo, $tiposAceptados, true) || $coincideEtiqueta) {
                $archivo = trim((string)($documento['archivo'] ?? ''));
                if ($archivo !== '') break;
            }
        }
    }

    if (!$esLicencia && $archivo === '') {
        // Compatibilidad con constancias guardadas en el campo antiguo.
        $archivo = trim((string)($tramite['formato_constancia'] ?? ''));
    }

    $url = rutaPublicaDocumentoEscaneado($archivo);
    return [
        'tipo' => $esLicencia ? 'licencia' : 'constancia',
        'etiqueta' => $esLicencia ? 'Ver licencia' : 'Ver constancia',
        'url' => $url,
        'disponible' => $url !== ''
    ];
}

function rutaPublicaDocumentoEscaneado(string $archivo): string
{
    // Normaliza separadores y elimina el prefijo relativo antes de validar.
    $archivo = str_replace('\\', '/', trim($archivo));
    $archivo = preg_replace('#^\./#', '', $archivo);
    if ($archivo === '' || strpos($archivo, '..') !== false) return '';

    $relativa = strpos($archivo, 'uploads/') === 0
        ? substr($archivo, strlen('uploads/'))
        : $archivo;
    $relativa = ltrim($relativa, '/');
    if ($relativa === '') return '';

    // Verifica que el archivo exista y permanezca dentro de la carpeta uploads.
    $base = realpath(__DIR__ . '/../uploads');
    $real = realpath(__DIR__ . '/../uploads/' . $relativa);
    if ($base === false || $real === false || !is_file($real)) return '';

    $baseNormalizada = rtrim(str_replace('\\', '/', $base), '/') . '/';
    $realNormalizada = str_replace('\\', '/', $real);
    if (strpos($realNormalizada, $baseNormalizada) !== 0) return '';

    return 'uploads/' . implode('/', array_map('rawurlencode', explode('/', $relativa)));
}
