# Especificación de Requisitos de Software (SRS)
## SisDiT — Sistema Único de Simplificación y Digitalización de Trámites

**Institución:** Municipio de Rincón de Romos, Aguascalientes  
**Área funcional:** Planeación y Desarrollo Urbano  
**Versión del documento:** 1.0  
**Fecha:** 22 de septiembre de 2026  
**Estado:** Borrador para validación funcional y técnica  
**Base de elaboración:** Inspección del código fuente y del esquema SQL disponibles en el proyecto.

## 1. Introducción

### 1.1 Propósito

Este documento especifica los requisitos de SisDiT para registrar, consultar, revisar y dar seguimiento a trámites municipales, integrar sus expedientes y relacionarlos con información territorial. Su finalidad es proporcionar una base común para el área usuaria, desarrollo, pruebas, administración del sistema y responsables de aceptar futuras entregas.

Los requisitos se expresan como comportamientos verificables. La existencia de código relacionado constituye evidencia de alcance, pero no demuestra por sí misma que cada requisito esté satisfecho en producción. No se realizaron pruebas funcionales contra una base de datos ni entrevistas con las personas responsables del proceso.

### 1.2 Alcance

SisDiT es una aplicación web que centraliza cuentas de usuario, recepción de solicitudes, expedientes documentales, revisión de trámites, información catastral, representación en mapas, elaboración de documentos de salida y seguimiento hasta la entrega y archivo.

El catálogo disponible contiene: Constancia de Número Oficial, Constancia de Compatibilidad Urbanística, Fusión de Predios, Subdivisión de Predio, Informe de Compatibilidad Urbanística, Terminación de Obra, Licencia de Construcción, Anuncios Publicitarios y Visto Bueno. La presencia de un tipo en el catálogo no garantiza que cuente con un formulario o una plantilla específica terminados.

El sistema dispone de interfaces para Usuario, Ventanilla, Verificador, Calificador y Administrador. El portal se presenta como herramienta de uso interno municipal; la existencia de un rol Usuario y de solicitudes de registro no basta para considerar autorizado un servicio público de autoservicio ciudadano.

### 1.3 Exclusiones

No se incluyen en esta versión requisitos de pago en línea, facturación, firma electrónica certificada, aplicación móvil nativa ni integración automática con plataformas externas de catastro. La marca de estado «Firmado» representa el registro administrativo de una firma; no acredita una firma criptográfica. La normativa aplicable, los plazos oficiales y las políticas de conservación deberán ser proporcionados y validados por la institución.

### 1.4 Convenciones y definiciones

| Término | Definición |
| --- | --- |
| SRS | Especificación de Requisitos de Software. |
| RF | Requisito funcional. |
| RNF | Requisito no funcional. |
| Expediente | Datos, documentos, evidencias y movimientos relacionados con un trámite. |
| Folio de ingreso | Referencia de recepción compuesta por número y año; puede agrupar registros relacionados. |
| Folio de salida | Identificador asignado a un documento o trámite de salida según su secuencia. |
| Cuenta catastral | Referencia utilizada para identificar y agrupar información del predio. |
| LC | Licencia de Construcción. |
| GeoJSON | Formato de intercambio de datos geográficos utilizado por el sistema. |
| Evidenciado | Se localizaron elementos de código relacionados; requiere validación funcional. |
| Propuesto | Requisito objetivo cuya aprobación o cumplimiento todavía debe confirmarse. |

La prioridad Alta identifica funciones esenciales para la operación o la integridad de los datos. La prioridad Media identifica funciones de apoyo. Todos los criterios de aceptación de este documento son pruebas por ejecutar, no resultados obtenidos.

## 2. Descripción general

### 2.1 Perspectiva del producto

La solución utiliza páginas PHP, interfaces HTML/CSS/JavaScript, sesiones de usuario y una base de datos MySQL/MariaDB mediante MySQLi. El entorno de trabajo observado utiliza XAMPP. Los documentos se almacenan como archivos vinculados a registros y existen mecanismos de entrega controlada de archivos. El mapa incorpora Leaflet, Proj4 y cartografía de OpenStreetMap; varias bibliotecas visuales se cargan desde servicios externos. Hay código de generación de PDF que requiere mPDF y su cargador de dependencias.

### 2.2 Actores

| Actor | Responsabilidad prevista |
| --- | --- |
| Usuario | Acceder a las funciones habilitadas y consultar sus propios expedientes conforme a la autorización de cada operación. |
| Ventanilla | Recibir y gestionar expedientes, tramitar solicitudes de LC, registrar firma y documentar entrega y archivo. |
| Verificador | Revisar información y evidencias, registrar observaciones y enviar trámites a firma. |
| Calificador | Trabajar con compatibilidad urbanística y licencia de construcción, así como datos de salida habilitados para su perfil. |
| Administrador | Administrar usuarios y solicitudes de acceso, configurar el sistema y realizar operaciones habilitadas para los perfiles operativos. |
| Solicitante o propietario | Persona cuyos datos forman parte del expediente; no implica que disponga de una cuenta. |

La matriz definitiva de permisos deberá aprobarse por operación. Se observaron diferencias entre las reglas generales de acceso a trámites y algunos controladores de archivos; no debe asumirse que todos los perfiles internos tienen permisos idénticos.

### 2.3 Supuestos y dependencias

- La institución suministrará catálogos, cartografía, plantillas y datos institucionales correctos.
- La instalación deberá contar con las extensiones PHP utilizadas, permisos de almacenamiento y esquema compatible con el código desplegado.
- Los mapas y recursos alojados en servicios externos requieren conectividad, salvo que se habiliten alternativas locales.
- El envío de correo depende de la configuración y disponibilidad del servicio correspondiente.
- La conservación documental, los respaldos y la administración de cuentas tendrán responsables institucionales designados.

## 3. Requisitos funcionales

### 3.1 Identidad y acceso

| ID / prioridad | Requisito | Criterio de aceptación | Base |
| --- | --- | --- | --- |
| RF-01 / Alta | El sistema deberá autenticar mediante correo y contraseña a las cuentas activas y dirigirlas al tablero de su rol. | Una cuenta activa con credenciales válidas accede al tablero correspondiente; una contraseña incorrecta o una cuenta inactiva no crea una sesión autorizada. | Evidenciado: php/login.php. |
| RF-02 / Alta | El sistema deberá gestionar solicitudes de registro con aprobación o rechazo por el administrador. | Aprobar una solicitud pendiente crea la cuenta y registra resolución y responsable; rechazar conserva el resultado y motivo. | Evidenciado: php/registro.php y php/gestion_solicitudes.php. |
| RF-03 / Alta | El administrador deberá poder crear, editar, activar, desactivar y solicitar la eliminación de usuarios según las restricciones de integridad. | Un perfil distinto del administrador no ejecuta estas operaciones; las modificaciones autorizadas quedan registradas. | Evidenciado: php/gestion_usuarios.php. |
| RF-04 / Alta | El sistema deberá permitir recuperar la contraseña mediante un token con vigencia limitada y cerrar la sesión. | Un token vencido no permite cambiar la contraseña y una sesión cerrada no permite acceder a recursos protegidos. | Evidenciado: php/recuperar.php, php/reset_password.php y logout.php. |
| RF-05 / Alta | Cada operación deberá verificar sesión, rol y acceso al expediente solicitado. | Un usuario sin permiso no consulta ni modifica un expediente o archivo al cambiar su identificador en la petición. | Evidenciado parcialmente: php/funciones_seguridad.php y controladores; cobertura por validar. |

### 3.2 Registro y consulta de expedientes

| ID / prioridad | Requisito | Criterio de aceptación | Base |
| --- | --- | --- | --- |
| RF-06 / Alta | El sistema deberá registrar tipo de trámite, propietario, dirección, localidad, fecha de ingreso, solicitante y teléfono como datos obligatorios del alta. | La omisión de cualquiera de estos campos impide guardar y presenta el error correspondiente. | Evidenciado: php/tramite.php. |
| RF-07 / Alta | El expediente deberá admitir cuenta catastral, colonia, código postal, superficie, referencias y coordenadas según el trámite. | Los datos capturados se recuperan en la consulta del mismo registro; la cuenta catastral mantiene su representación textual normalizada. | Evidenciado: php/tramite.php y database/sistema.sql. |
| RF-08 / Alta | El sistema deberá conservar la identidad individual de los trámites y sus relaciones de agrupación por folio o trámite principal. | Dos registros relacionados pueden consultarse individualmente por su ID sin confundir documentos o estados. | Evidenciado: php/actualizarTramite.php y esquema de tramites. |
| RF-09 / Alta | Los tableros deberán permitir consultar y filtrar los trámites conforme al perfil y los filtros disponibles. | Un filtro por folio o fecha devuelve registros coincidentes dentro del alcance autorizado. | Evidenciado: DashUser.php, DashVentanilla.php, DashVer.php y DashCalf.php. |
| RF-10 / Media | El sistema deberá permitir buscar antecedentes catastrales y reutilizar documentos de trámites autorizados. | Una búsqueda recupera los antecedentes coincidentes y la copia vincula los documentos al destino correcto. | Evidenciado: php/buscar_catastral.php, php/obtener_tramite_anterior.php y php/copiar_documentos_tramite.php. |
| RF-11 / Alta | El sistema deberá permitir adjuntar documentos y fotografías con validación de extensión, contenido MIME y tamaño. | Un archivo incompatible o mayor al límite del controlador se rechaza sin incorporarse al expediente; un archivo válido queda asociado al registro correcto. | Evidenciado: php/Utilidades.php; límite general de 10 MiB, sujeto a reglas específicas del controlador. |

### 3.3 Revisión, firma y archivo

| ID / prioridad | Requisito | Criterio de aceptación | Base |
| --- | --- | --- | --- |
| RF-12 / Alta | El sistema deberá gestionar estados de revisión, revisión por validador, corrección y rechazo, junto con observaciones. | Una actualización autorizada conserva el estado y las observaciones y permite recuperar el movimiento en el historial. | Evidenciado: php/actualizarTramite.php. |
| RF-13 / Alta | Solo Verificador o Administrador deberá enviar un trámite al estado Pendiente por firmar. | Una petición equivalente de otro rol se rechaza en el servidor. | Evidenciado: php/actualizarTramite.php. |
| RF-14 / Alta | Solo Ventanilla o Administrador deberá registrar Firmado desde Pendiente por firmar. | El servidor rechaza la firma si el estado anterior o el perfil no corresponde. | Evidenciado: php/actualizarTramite.php. |
| RF-15 / Alta | Solo Ventanilla o Administrador deberá registrar Entregado y archivado desde Firmado y con el documento firmado adjunto. | Sin escaneo, con archivo inválido o con estado anterior distinto de Firmado, no se completa el archivo. | Evidenciado: php/actualizarTramite.php. |
| RF-16 / Alta | El sistema deberá mantener historial de movimientos con usuario, estado anterior, estado nuevo, fecha y comentario cuando corresponda. | Tras una transición exitosa es posible identificar qué cambió, cuándo y quién la realizó. | Evidenciado: historial_tramites y php/actualizarTramite.php. |
| RF-17 / Media | El sistema deberá intentar notificar por correo los eventos habilitados cuando exista destinatario. | En un entorno de correo configurado se recibe el mensaje del evento; un fallo de envío no debe presentarse como entrega confirmada. | Evidenciado parcialmente: php/actualizarTramite.php y php/gestion_solicitudes.php; manejo completo de fallos por validar. |

### 3.4 Documentos, calificación y licencia de construcción

| ID / prioridad | Requisito | Criterio de aceptación | Base |
| --- | --- | --- | --- |
| RF-18 / Alta | El sistema deberá elaborar los documentos de salida disponibles con datos del expediente y configuración institucional. | La vista o documento generado corresponde al trámite seleccionado y muestra los datos guardados aplicables. | Evidenciado: constancia_numero.php, constancia_compatibilidad.php, licencia_construccion.php e imprimir_documentos.php. |
| RF-19 / Alta | El sistema deberá administrar folios de salida según el tipo de trámite y año, preservando la asociación con el registro individual. | Dos asignaciones simultáneas de una misma secuencia no producen un folio de salida duplicado. | Evidenciado: php/actualizarTramite.php y folios_salida_secuencia; concurrencia por verificar. |
| RF-20 / Alta | Ventanilla o Administrador deberá poder guardar y aprobar la solicitud de LC vinculada al trámite. | La aprobación exige descripción y tipo de obra, una solicitud pendiente y registra responsable y fecha; una solicitud aprobada no se aprueba de nuevo. | Evidenciado: php/guardar_solicitud_lc.php y php/aprobar_solicitud_lc.php. |
| RF-21 / Alta | El módulo de Calificador deberá permitir consultar compatibilidad urbanística y LC y gestionar los datos de salida autorizados. | El tablero presenta los tipos 2 y 7; los cambios de salida se recuperan al consultar el mismo trámite. | Evidenciado: DashCalf.php y php/guardar_tramite_salida.php. |
| RF-22 / Media | El sistema deberá permitir descargar o imprimir las solicitudes y documentos disponibles, respetando autorización y dependencias de generación. | El archivo corresponde al registro pedido y un usuario sin permiso no puede obtenerlo. | Evidenciado: php/descargar_pdf.php y php/descargar_solicitud_lc.php; cobertura de permisos por validar. |

### 3.5 Información territorial y administración

| ID / prioridad | Requisito | Criterio de aceptación | Base |
| --- | --- | --- | --- |
| RF-23 / Alta | El sistema deberá representar en el mapa los trámites con datos geográficos válidos y permitir consultar su información asociada. | Un trámite georreferenciado se muestra en la ubicación guardada y su selección recupera el expediente correcto. | Evidenciado: mapa_tramites.php y php/get_tramites_geojson.php. |
| RF-24 / Alta | El sistema deberá guardar y recuperar croquis y polígonos asociados a predios o trámites. | Tras guardar un croquis, su consulta conserva la geometría y la relación con el registro. | Evidenciado: php/guardar_croquis_mapa.php, php/asignar_croquis.php y php/obtener_croquis_poligono.php. |
| RF-25 / Media | El sistema deberá agrupar información por cuenta catastral y aplicar las reglas de visibilidad de estados aprobados en las vistas pertinentes. | Registros con la misma cuenta normalizada se agrupan y los estados se interpretan de acuerdo con la regla de la vista. | Evidenciado: php/Utilidades.php y tests/run.php. |
| RF-26 / Media | El sistema deberá presentar estadísticas de seguimiento basadas en los trámites accesibles. | Los totales mostrados coinciden con un conjunto controlado de registros y filtros. | Evidenciado: php/obtener_estadisticas.php. |
| RF-27 / Alta | El sistema deberá restringir la edición de parámetros y datos de constancias a los perfiles autorizados. | Una modificación permitida se refleja en documentos posteriores; una petición no autorizada no altera la configuración. | Evidenciado: php/actualizar_configuracion.php y php/actualizar_config_constancia.php; matriz exacta por validar. |

## 4. Reglas de negocio y estados

| ID | Regla |
| --- | --- |
| RN-01 | El ID del trámite identifica el registro individual. No se deberá tratar el folio de ingreso como identificador único cuando existan trámites agrupados. |
| RN-02 | El estado Firmado requiere que el estado anterior sea Pendiente por firmar. |
| RN-03 | Entregado y archivado requiere el estado anterior Firmado y un documento firmado válido. |
| RN-04 | La aprobación de la solicitud de LC y el estado general del trámite son conceptos distintos; aprobar la solicitud no implica por sí mismo la entrega del expediente. |
| RN-05 | Los folios de salida deben conservar la secuencia aplicable por tipo y año, sin confundirse con el folio de ingreso. |
| RN-06 | Las operaciones sobre expedientes y documentos deben validar permisos en el servidor, aunque la interfaz oculte sus controles. |
| RN-07 | Los documentos exigibles y la posibilidad de justificar faltantes se definirán por tipo de trámite con el área responsable. Pendiente de validación institucional. |
| RN-08 | Los plazos incluidos en descripciones del catálogo no constituyen por sí solos un calendario oficial ni un compromiso de resolución automática. |

El flujo de referencia es: recepción en «En revisión», revisión operativa y, cuando corresponda, «En Revisión por Validador», seguido de «Pendiente por firmar», «Firmado» y «Entregado y archivado». Existen salidas de «En corrección» y «Rechazado». Las transiciones exactas de entrada y retorno de corrección, reapertura y rechazo deberán aprobarse antes de considerarlas una máquina de estados completa. El código examinado impone explícitamente las restricciones finales de firma y archivo, pero no acredita todas las demás transiciones posibles.

## 5. Requisitos de datos

| Entidad lógica | Información y relaciones principales |
| --- | --- |
| Usuarios y solicitudes de acceso | Identidad, correo, rol, estado de cuenta, credenciales protegidas y resolución de solicitudes. |
| Trámites | ID, folio y año, tipo, propietario, solicitante, ubicación, contacto, cuenta catastral, fechas, estado, creador y referencias documentales. |
| Tipos de trámite | Código, nombre, descripción, disponibilidad y referencia de plantilla cuando exista. |
| Documentos y constancias | Archivo o datos de generación, trámite relacionado, folio de salida y metadatos correspondientes. |
| Solicitud de LC y salida | Datos especializados de obra, estado de aprobación y datos del documento de salida; dependen del esquema complementario. |
| Cartografía y catálogos | Calles, códigos postales, catastro, croquis, polígonos y sus detalles. |
| Historial y bitácora | Usuario, acción, registro afectado, estados, fechas y detalles del evento. |
| Configuración y secuencias | Valores institucionales y control de consecutivos de salida. |

Los identificadores y relaciones deberán impedir asociaciones con registros inexistentes. Fechas y horas deberán interpretarse bajo una zona horaria acordada. Los campos de cuenta catastral se tratarán como identificadores textuales para evitar pérdida de ceros. Las operaciones que alteren varias tablas deberán conservar su consistencia ante errores. La eliminación de registros con dependencias y la conservación histórica requieren una política institucional explícita.

## 6. Interfaces externas

### 6.1 Interfaz de usuario

La interfaz deberá presentar formularios, tableros y mensajes en español; identificar campos obligatorios; informar errores de validación; y permitir reconocer el expediente y su estado. Los controles visibles se adaptarán al rol. Se deberán conservar caracteres propios del español en captura, almacenamiento y documentos.

### 6.2 Interfaces de software

La aplicación se comunica con la base de datos mediante MySQLi y utiliza peticiones de formulario y respuestas JSON para operaciones internas. La cartografía se intercambia mediante GeoJSON y se presenta con Leaflet. La generación PDF depende de mPDF en las rutas que lo utilizan. No se especifica una API pública versionada, ya que no se identificó un contrato de integración de ese tipo.

### 6.3 Comunicaciones y archivos

El despliegue productivo deberá utilizar HTTPS. El acceso a documentos privados deberá realizarse mediante controladores autorizados. Se deberá comprobar el funcionamiento de la mensajería y la cartografía en la red municipal. Los formatos admitidos dependerán del campo y controlador: la validación general contempla PDF, JPG, JPEG y PNG, pero no representa una lista universal para todos los módulos.

## 7. Requisitos no funcionales

Los objetivos numéricos siguientes son propuestas de aceptación; no son capacidades medidas ni compromisos ya aprobados.

| ID / prioridad | Requisito verificable | Estado |
| --- | --- | --- |
| RNF-01 / Alta | Aplicar control de sesión y autorización a todas las rutas protegidas, incluidas descargas y operaciones AJAX; una prueba de acceso cruzado no deberá revelar expedientes ajenos. | Controles evidenciados; cobertura integral pendiente. |
| RNF-02 / Alta | Almacenar contraseñas con hash, validar CSRF en operaciones de cambio, emplear consultas parametrizadas para entradas y escapar contenido al mostrarlo. Verificar con revisión de rutas y casos negativos. | Mecanismos evidenciados; cobertura integral pendiente. |
| RNF-03 / Alta | Rechazar archivos cuyo contenido o tamaño no esté permitido y evitar la ejecución o el acceso directo no autorizado a documentos privados. | Validación evidenciada; configuración del servidor por verificar. |
| RNF-04 / Alta | Ante un fallo de escritura, una transacción deberá conservar los datos previos sin dejar una actualización parcial del expediente; comprobar mediante fallo controlado. | Transacciones evidenciadas; prueba pendiente. |
| RNF-05 / Alta | Registrar acciones relevantes con usuario, fecha y registro afectado, sin incluir contraseñas ni tokens completos en la bitácora. | Bitácora evidenciada; revisión de contenido pendiente. |
| RNF-06 / Media | Con 20 sesiones concurrentes y 10 000 trámites de prueba, el percentil 95 de consultas ordinarias deberá ser de 3 segundos o menos, excluyendo transferencia de archivos y servicios externos. | Propuesto; infraestructura y carga por aprobar. |
| RNF-07 / Media | Alcanzar 99 % de disponibilidad durante el horario institucional acordado, excluyendo mantenimiento autorizado y documentando interrupciones. | Propuesto; horario y medición por definir. |
| RNF-08 / Alta | Ejecutar respaldos diarios de base de datos y archivos, con pérdida máxima objetivo de 24 horas y recuperación objetivo de 8 horas, demostrada mediante restauración. | Propuesto; responsables y capacidad por validar. |
| RNF-09 / Media | Permitir las operaciones principales con teclado, etiquetas comprensibles y errores visibles; comprobar el flujo de captura y consulta sin ratón. | Propuesto; evaluación de accesibilidad pendiente. |
| RNF-10 / Media | Validar los flujos críticos en los navegadores institucionales acordados y a resoluciones de 1366 × 768 y 390 × 844, sin ocultar acciones esenciales. | Propuesto; versiones de navegador por fijar. |
| RNF-11 / Alta | Documentar instalación, dependencias, cambios de esquema y restauración de manera que una instalación limpia pueda reproducirse sin datos personales reales. | Propuesto; se detectaron dependencias de migración pendientes. |
| RNF-12 / Alta | Restringir consulta, exportación y conservación de datos personales conforme a una política institucional aprobada y verificable mediante la matriz de acceso. | Propuesto; política y plazos pendientes. |

## 8. Casos de uso principales

### CU-01. Registrar un expediente

**Actor:** Personal o cuenta con permiso de captura. **Precondición:** Sesión válida y tipo de trámite disponible. El actor captura los datos obligatorios, agrega datos complementarios y adjuntos, y solicita guardar. El sistema valida entradas, registra el trámite y sus relaciones y devuelve una referencia de consulta. **Alternativa:** Ante datos faltantes o archivos inválidos, informa el error sin completar un registro inconsistente. **Resultado:** Expediente identificable y consultable. **Requisitos:** RF-05 a RF-11.

### CU-02. Revisar y enviar a firma

**Actor:** Verificador o Administrador. **Precondición:** Expediente accesible para revisión. El actor consulta datos y evidencias, registra observaciones y, si procede, envía a Pendiente por firmar. **Alternativa:** Registra corrección o rechazo según las reglas aprobadas. **Resultado:** Estado e historial actualizados. **Requisitos:** RF-12, RF-13 y RF-16.

### CU-03. Registrar firma, entrega y archivo

**Actor:** Ventanilla o Administrador. **Precondición:** Trámite Pendiente por firmar. El actor registra la firma, obtiene el estado Firmado y, al entregar, adjunta el documento firmado para archivar. **Alternativa:** Un estado incompatible o un escaneo ausente o inválido impide la transición correspondiente. **Resultado:** Trámite Entregado y archivado con evidencia documental. **Requisitos:** RF-14 a RF-16.

### CU-04. Gestionar solicitud de LC

**Actor:** Ventanilla o Administrador. **Precondición:** Trámite de LC existente. El actor guarda la solicitud, completa tipo y descripción de obra y solicita su aprobación. El sistema comprueba que siga pendiente y registra responsable y fecha. **Alternativa:** Si faltan datos o ya fue aprobada, rechaza la operación. **Resultado:** Solicitud aprobada vinculada al expediente, sin asumir cierre del trámite general. **Requisitos:** RF-20 a RF-22.

### CU-05. Consultar información territorial

**Actor:** Personal autorizado. **Precondición:** Datos geográficos disponibles. El actor accede al mapa, localiza el predio o trámite y consulta los datos y el croquis asociados. **Alternativa:** Si no hay geometría o falla la cartografía externa, deberá informarse la ausencia sin inventar una ubicación. **Resultado:** Consulta territorial vinculada al expediente. **Requisitos:** RF-23 a RF-25; mensaje de contingencia por validar.

## 9. Trazabilidad y estrategia de aceptación

| Grupo de requisitos | Evidencia principal | Validación prevista |
| --- | --- | --- |
| RF-01 a RF-05 | Acceso, sesiones, registro, recuperación y administración de usuarios. | Matriz de roles, cuentas inactivas, tokens vencidos y acceso cruzado. |
| RF-06 a RF-11 | Captura, tableros, utilidades y documentos. | Altas válidas e inválidas, filtros, agrupación y carga de archivos. |
| RF-12 a RF-17 | Actualización de trámite, historial y notificaciones. | Transiciones permitidas y denegadas, escaneo obligatorio y fallo de correo. |
| RF-18 a RF-22 | Constancias, solicitudes LC, salida e impresión. | Consistencia documental, aprobación de LC y concurrencia de folios. |
| RF-23 a RF-27 | Mapa, croquis, estadísticas y configuración. | Integridad geográfica, totales controlados y restricciones administrativas. |
| RNF-01 a RNF-12 | Controles transversales e infraestructura. | Seguridad, carga, restauración, compatibilidad y operación. |

La aceptación deberá realizarse en un entorno de pruebas con datos ficticios y una cuenta por rol. Se ejecutarán primero los escenarios de prioridad Alta y sus casos negativos. Cada resultado deberá registrar requisito, datos de entrada, resultado esperado, resultado real y evidencia. Los errores que permitan acceso indebido, pérdida de datos o transiciones finales inválidas impedirán aceptar la entrega.

El repositorio incluye tests/run.php y tests/README.md con pruebas de validación MIME/tamaño, normalización de estados, visibilidad de polígonos y agrupación catastral. Estas pruebas no sustituyen la validación integral del flujo ni verifican por sí mismas la base de datos o el despliegue. No se ejecutaron como parte de la elaboración de este documento.

## 10. Pendientes, restricciones y riesgos identificados

| ID | Hallazgo o decisión pendiente | Consecuencia y resolución requerida |
| --- | --- | --- |
| P-01 | El SQL base conserva estados como Aprobado y Aprobado por Verificador, mientras el controlador contempla Pendiente por firmar, Firmado y Entregado y archivado. | Alinear esquema y código y definir la equivalencia de estados históricos antes de validar el flujo. |
| P-02 | php/migrate_flujo_firma_archivo.php referencia migrate_flujo_firma_archivo.sql en la raíz; ese archivo no se encontró en la revisión. | Recuperar o preparar la migración y verificarla en una instalación de prueba. No se infiere el estado de la base desplegada. |
| P-03 | Los módulos consultan estructuras complementarias como solicitud_lc y tramites_salida que no aparecen entre los CREATE TABLE del SQL base revisado. | Consolidar la secuencia de instalación y comprobar las migraciones complementarias. |
| P-04 | Los permisos generales de personal y los de ciertos controladores de archivos no son idénticos. | Aprobar una matriz por operación y verificarla, especialmente para Calificador y Usuario. |
| P-05 | El catálogo contiene tipos sin plantilla PDF declarada. | Validar el alcance real de emisión para cada tipo; no dar por terminadas plantillas por la sola existencia del catálogo. |
| P-06 | No se dispone de aprobación institucional de plazos, requisitos documentales, conservación ni métricas de servicio. | Validar estas políticas antes de convertir las propuestas del SRS en compromisos de aceptación. |
| P-07 | Hay directorios estadias con variantes históricas del proyecto. | Esta especificación toma como referencia la aplicación de la raíz; confirmar la versión destinada al despliegue. |

## 11. Control del documento y aprobación

| Versión | Fecha | Descripción |
| --- | --- | --- |
| 1.0 | 22/09/2026 | Primera especificación elaborada a partir del código disponible; pendiente de validación institucional. |

| Responsabilidad | Nombre | Fecha y conformidad |
| --- | --- | --- |
| Responsable del área usuaria | Por designar | Pendiente |
| Responsable técnico | Por designar | Pendiente |
| Responsable de pruebas y aceptación | Por designar | Pendiente |

La aprobación deberá confirmar alcance, permisos, transiciones, catálogo de documentos y objetivos de operación. Los cambios posteriores deberán identificar los requisitos afectados y actualizar su criterio de aceptación y versión documental.
