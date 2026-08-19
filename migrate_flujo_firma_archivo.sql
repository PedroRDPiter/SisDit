-- Ejecutar una vez antes de desplegar el nuevo flujo de firma y archivo.
ALTER TABLE tramites
  MODIFY estatus ENUM(
    'En revisión',
    'En Revisión por Validador',
    'Aprobado por Verificador',
    'Aprobado',
    'Pendiente por firmar',
    'Firmado',
    'Entregado y archivado',
    'Rechazado',
    'En corrección'
  ) NOT NULL DEFAULT 'En revisión';

-- Repara valores vacíos que MySQL aceptó cuando el código intentó guardar uno
-- de los nuevos estatus antes de aplicar esta ampliación del ENUM.
UPDATE tramites t
SET t.estatus = COALESCE(
  (
    SELECT h.estatus_nuevo
    FROM historial_tramites h
    WHERE h.tramite_id = t.id
      AND h.estatus_nuevo IN (
        'En revisión',
        'En Revisión por Validador',
        'Aprobado por Verificador',
        'Aprobado',
        'Pendiente por firmar',
        'Firmado',
        'Entregado y archivado',
        'Rechazado',
        'En corrección'
      )
    ORDER BY h.id DESC
    LIMIT 1
  ),
  'En revisión'
)
WHERE t.estatus = '';

ALTER TABLE historial_tramites
  MODIFY accion ENUM(
    'Creado',
    'Modificado',
    'Enviado a revisión',
    'Aprobado por Verificador',
    'Aprobado',
    'Pendiente por firmar',
    'Firmado',
    'Entregado y archivado',
    'Rechazado',
    'En corrección',
    'En Revisión por Validador'
  ) NOT NULL;

UPDATE historial_tramites
SET accion = CASE
  WHEN estatus_nuevo IN (
    'Pendiente por firmar',
    'Firmado',
    'Entregado y archivado',
    'En Revisión por Validador',
    'Rechazado',
    'En corrección'
  ) THEN estatus_nuevo
  ELSE 'Modificado'
END
WHERE accion = '';

-- Conserva los trámites que estaban esperando la firma en el flujo anterior.
UPDATE tramites
SET estatus = 'Pendiente por firmar'
WHERE estatus = 'Aprobado por Verificador';

-- Los valores anteriores permanecen en el ENUM para no reinterpretar ni perder
-- el historial de trámites que ya habían concluido antes de este cambio.
