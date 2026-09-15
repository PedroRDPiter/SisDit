# Actualizar polígonos desde el administrador

En **Actualizar SHP**, seleccionar el archivo `.shp` municipal y pulsar
**Cambiar a GeoJSON y actualizar mapas**. El resultado muestra los polígonos,
claves recuperadas, predios sin clave y geometrías omitidas. Recargar los mapas
que estaban abiertos para ver la actualización.

Se acepta un SHP de polígonos (Polygon, PolygonZ o PolygonM), hasta 30 MB,
en WGS84 / UTM zona 13 norte. Sin el archivo PRJ no es posible identificar
automáticamente otros sistemas de referencia. Se verifica que las coordenadas
convertidas estén en el entorno del municipio.

El SHP no contiene atributos DBF. Solo se conservan atributos de la capa vigente
cuando coincide el conjunto completo de vértices a siete decimales y no hay
atributos ambiguos. Los predios nuevos o modificados quedan sin clave; no se
asignan claves por cercanía. No se modifican los trámites ni los croquis guardados.

## Preparación del servidor

- Python con las dependencias de `scripts/requirements-poligonos.txt`.
- `SISDIT_PYTHON` puede indicar la ruta del ejecutable de Python. En este equipo
  se usa `C:/ProgramData/anaconda3/python.exe`; en otros equipos se busca `python`.
- PHP debe permitir `proc_open`, disponer de 180 segundos de ejecución y permitir
  cargas de 30 MB (por ejemplo, `upload_max_filesize=40M`, `post_max_size=40M`).
- La cuenta del servidor necesita leer las dependencias Python y escribir en
  `Geojson` y en el directorio temporal.

La conversión se ejecuta en un archivo temporal. Solo al finalizar se respalda
la capa vigente en `Geojson/TRAMITES_reprojected.geojson.bak` y se reemplaza
`Geojson/TRAMITES_reprojected.geojson`, que comparten todos los mapas.
El respaldo corresponde a la versión inmediatamente anterior y su descarga
está bloqueada por las reglas de archivos `.bak` del proyecto.
Las actualizaciones simultáneas se bloquean y se registra la operación en el log.
