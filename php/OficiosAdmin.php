<?php
declare(strict_types=1);
require_once __DIR__ . '/Utilidades.php';

function validarAccionOficio(string $rol, string $accion, string $actual, string $esperado, bool $confirmado): string
{
    if ($rol !== 'Administrador') {
        throw new DomainException('Solo el administrador puede autorizar esta operación.', 403);
    }

    if (!in_array($accion, ['firmar', 'cerrar'], true)) {
        throw new DomainException('Acción no válida.', 422);
    }

    if ($actual !== $esperado) {
        throw new DomainException('El oficio cambió desde que lo abriste. Actualiza la bandeja antes de continuar.', 409);
    }

    if (!$confirmado) {
        throw new DomainException('Confirma la autorización antes de continuar.', 422);
    }

    $origen = $accion === 'firmar' ? 'Pendiente por firmar' : 'Firmado';

    if ($actual !== $origen) {
        throw new DomainException('El estado actual no permite esta operación.', 409);
    }

    return $accion === 'firmar' ? 'Firmado' : 'Entregado y archivado';
}

/** Todas las descargas pasan por el controlador de archivos con sesión. */
function urlDocumentoOficio(string $ruta): string
{
    $ruta = str_replace('\\', '/', trim($ruta));
    if (
        $ruta === ''
        || str_contains($ruta, "\0")
        || preg_match('#(^|/)\.\.(/|$)#', $ruta)
        || str_contains($ruta, ':')
    ) {
        return '';
    }

    $ruta = preg_replace('#^\./#', '', $ruta);
    $scope = str_starts_with($ruta, '.private/') ? 'private' : 'uploads';

    if ($scope === 'private') {
        $ruta = substr($ruta, 9);
    } elseif (str_starts_with($ruta, 'uploads/')) {
        $ruta = substr($ruta, 8);
    }

    if ($ruta === '' || str_starts_with($ruta, '/')) {
        return '';
    }

    return 'php/archivo.php?' . http_build_query(['scope' => $scope, 'path' => $ruta], '', '&', PHP_QUERY_RFC3986);
}

function documentosOficio(array $tramite): array
{
    $documentos = [];
    $campos = [
        'formato_constancia' => 'Constancia',
        'oficio_vobo' => 'Oficio de visto bueno',
        'ine_archivo' => 'Identificación',
        'escrituras_archivo' => 'Escrituras',
        'titulo_archivo' => 'Título',
        'predial_archivo' => 'Predial',
        'croquis_archivo' => 'Croquis',
        'foto1_archivo' => 'Fotografía 1',
        'foto2_archivo' => 'Fotografía 2',
    ];

    foreach ($campos as $campo => $label) {
        $url = urlDocumentoOficio((string)($tramite[$campo] ?? ''));
        if ($url !== '') {
            $documentos[] = [
                'label' => $label,
                'url' => $url,
                'final' => false,
            ];
        }
    }

    $otros = json_decode((string)($tramite['otros_archivos'] ?? ''), true);
    foreach (is_array($otros) ? array_reverse($otros) : [] as $doc) {
        if (!is_array($doc)) {
            continue;
        }

        $url = urlDocumentoOficio((string)($doc['archivo'] ?? ''));
        if ($url !== '') {
            $documentos[] = [
                'label' => (string)($doc['label'] ?? 'Documento adjunto'),
                'url' => $url,
                'final' => str_starts_with((string)($doc['tipo'] ?? ''), 'documento_firmado_'),
            ];
        }
    }

    return $documentos;
}

/** Solo reutilizar el PDF producido y registrado por la firma de este panel. */
function documentoFirmadoDigital(array $tramite): ?array
{
    $otros = json_decode((string) ($tramite['otros_archivos'] ?? ''), true);

    foreach (is_array($otros) ? array_reverse($otros) : [] as $doc) {
        if (!is_array($doc) || ($doc['origen_firma'] ?? '') !== 'visible_pdf') {
            continue;
        }

        $ruta = (string) ($doc['archivo'] ?? '');
        if (!preg_match('#^\.private/oficios/[a-zA-Z0-9_-]+\.pdf$#D', $ruta)) {
            return null;
        }

        $real = dirname(__DIR__) . '/' . $ruta;
        if (
            !is_file($real)
            || empty($doc['sha256'])
            || !hash_equals((string) $doc['sha256'], (string) hash_file('sha256', $real))
        ) {
            return null;
        }

        return $doc;
    }

    return null;
}
