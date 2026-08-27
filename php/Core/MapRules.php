<?php
declare(strict_types=1);

final class MapRules
{
    public const VISIBLE_STATUSES = ['Aprobado por Verificador', 'Aprobado', 'Pendiente por firmar', 'Firmado', 'Entregado y archivado'];

    public static function isApprovedForVentanilla(string $status): bool
    {
        return in_array(trim($status), self::VISIBLE_STATUSES, true);
    }

    public static function groupByCadastralAccount(array $rows): array
    {
        $grouped = [];
        foreach ($rows as $row) {
            $key = trim((string)($row['cuenta_catastral'] ?? $row['cuenta_catastral_origen'] ?? $row['numero_poligono'] ?? ''));
            if ($key === '') continue;
            $id = (int)($row['id'] ?? $row['tramite_id'] ?? 0);
            if (!isset($grouped[$key])) $grouped[$key] = [];
            if ($id > 0 && isset($grouped[$key][$id])) continue;
            $grouped[$key][$id > 0 ? $id : count($grouped[$key])] = $row;
        }
        return array_map('array_values', $grouped);
    }
}
