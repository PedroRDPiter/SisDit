# Firma visible y cierre digital de oficios

## Uso del administrador

1. Entra a **Panel de Administrador → Firma y cierre**. La bandeja separa **Por firmar**, **Listos para cerrar** y **Cerrados**, con búsqueda por folio, propietario y tipo.
2. Abre **Revisar y firmar** en un trámite que se encuentre **Pendiente por firmar**. Consulta sus documentos e historial.
3. Carga el PDF del oficio o selecciona **Usar para firmar** en un adjunto PDF. Los formatos disponibles también pueden abrirse, imprimirse como PDF y cargarse al editor.
4. Carga tu firma en PNG/JPG o dibújala y pulsa **Usar dibujo**. Selecciona la página, ajusta el tamaño y mueve la firma con los controles o arrastrándola.
5. Pulsa **Preparar PDF firmado** y abre **Revisar PDF firmado**. Confirma la autorización y pulsa **Guardar PDF y registrar firma**.
6. El oficio pasa a **Firmado**. Se conservan el PDF original y el PDF con la imagen de firma, junto con responsable, fecha y huellas SHA-256.
7. En **Listos para cerrar**, abre el expediente, confirma su entrega y pulsa **Cerrar y archivar oficio**. Se reutiliza el PDF firmado verificado. Los trámites firmados anteriormente, sin PDF generado por este editor, requieren adjuntar su documento final.
8. En **Cerrados → Ver expediente**, consulta el documento firmado y el historial.

La firma queda incrustada como imagen visible; este flujo no incorpora certificados de e.firma ni una firma criptográfica del PDF. La imagen se carga en cada operación y no se guarda como una firma reutilizable de la cuenta.

## Configuración

- La base local inspeccionada ya dispone de los estados y de `folios_salida_tipo_secuencia` necesarios. Para otra instalación con el esquema base, revisar y aplicar `database/migrations/20260922_firma_oficios.sql` en la base de destino.
- `tramites_salida` corresponde a la instalación existente de calificación/LC. Las plantillas de esos módulos siguen utilizando su propio identificador de salida.
- PHP necesita MySQLi, Fileinfo y Mbstring. Permitir escritura en `.private/oficios` y disponer de espacio para dos PDF por firma. Los documentos se entregan mediante `php/archivo.php`; el subdirectorio contiene una regla que deniega el acceso directo.
- El límite es 10 MiB por PDF y 2 MiB para la imagen de firma. La petición de firma lleva original y firmado: `post_max_size` debe ser mayor de 20 MiB y `upload_max_filesize` al menos 10 MiB. La configuración local revisada tiene ambos en 40M.
- PDF.js y pdf-lib están incluidos localmente con sus licencias en `assets/vendor`. Los PDF y las firmas se procesan en el navegador y se guardan en el servidor municipal; no se envían a servicios externos. El resto del panel conserva sus bibliotecas visuales por CDN.
- Solo cuentas activas con rol Administrador pueden usar el controlador de oficios. Se valida CSRF, estado esperado y confirmación. Las operaciones usan transacciones y bloqueo del trámite; una repetición no firma o cierra dos veces el mismo registro.
- Las observaciones de revisión y el responsable verificador se conservan. Las notas de firma/cierre se agregan al historial. La firma asigna folio de salida únicamente al trámite seleccionado cuando le falta; las reservas comparten la secuencia usada por calificación.

## Verificación

Las pruebas unitarias están en `tests/run.php`. Las pruebas del navegador están en `tests/admin-oficios.browser.cjs` y utilizan datos ficticios creados por `tests/fixture_oficios.php` en una base cuyo nombre debe comenzar por `sisdit_test_oficios_`. Nunca se deben ejecutar contra expedientes reales.

La prueba de navegador cubre firma dibujada, imagen PNG, PDF de dos páginas con rotación, presencia visual de la firma en el resultado, conservación del original, cierre reutilizando el PDF, historial, permisos, CSRF, archivos inválidos, repetición y aislamiento entre trámites con el mismo folio.
