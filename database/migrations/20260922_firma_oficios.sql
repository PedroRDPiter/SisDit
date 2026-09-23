-- Compatibilidad para instalaciones que todavía usan el esquema SQL base.
-- No transforma estados históricos ni modifica expedientes existentes.
ALTER TABLE tramites MODIFY estatus ENUM(
 'En revisión', 'En Revisión por Validador', 'Aprobado por Verificador', 'Aprobado',
 'Pendiente por firmar', 'Firmado', 'Entregado y archivado', 'Rechazado', 'En corrección'
) NOT NULL DEFAULT 'En revisión';

ALTER TABLE historial_tramites MODIFY accion ENUM(
 'Creado', 'Modificado', 'Enviado a revisión', 'Aprobado por Verificador', 'Aprobado',
 'Pendiente por firmar', 'Firmado', 'Entregado y archivado', 'Rechazado', 'En corrección',
 'En Revisión por Validador'
) NOT NULL;

CREATE TABLE IF NOT EXISTS folios_salida_tipo_secuencia (
 tipo_tramite_id INT NOT NULL,
 anio INT NOT NULL,
 ultimo_numero INT NOT NULL DEFAULT 0,
 PRIMARY KEY (tipo_tramite_id, anio),
 CONSTRAINT fk_folio_salida_tipo FOREIGN KEY (tipo_tramite_id) REFERENCES tipos_tramite(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
