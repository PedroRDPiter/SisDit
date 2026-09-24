<?php
declare(strict_types=1);

/** Reservar dentro de la transacción que guarda el trámite. */
function reservarFolioSalida(mysqli $conn, int $tipo, int $anio): int
{
    $stmt = $conn->prepare('SELECT COALESCE(MAX(folio_salida_numero), 0) + 1 AS siguiente FROM tramites WHERE tipo_tramite_id = ? AND folio_salida_anio = ?');
    $stmt->bind_param('ii', $tipo, $anio);
    $stmt->execute();
    $candidato = (int)$stmt->get_result()->fetch_assoc()['siguiente'];
    $stmt->close();
    // La clave (tipo, año) serializa reservas de todos los paneles.
    $stmt = $conn->prepare(
        'INSERT INTO folios_salida_tipo_secuencia (tipo_tramite_id, anio, ultimo_numero)
         VALUES (?, ?, LAST_INSERT_ID(?))
         ON DUPLICATE KEY UPDATE
         ultimo_numero = LAST_INSERT_ID(GREATEST(ultimo_numero + 1, VALUES(ultimo_numero)))'
    );
    $stmt->bind_param('iii', $tipo, $anio, $candidato);
    $stmt->execute();
    $numero = (int)$conn->insert_id;
    $stmt->close();
    if ($numero < 1) throw new RuntimeException('No se pudo reservar el folio de salida.');
    return $numero;
}
