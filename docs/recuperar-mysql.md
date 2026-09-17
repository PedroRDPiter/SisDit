# Recuperar MySQL cuando XAMPP no inicia

Abrir `Recuperar-MySQL.bat` con doble clic. No requiere que SisDiT o Apache funcionen.

1. **Diagnosticar:** muestra procesos, puertos y las últimas entradas del registro.
2. **Respaldar:** con MySQL detenido, copia toda la carpeta `data` y verifica sus
   archivos con SHA-256. Guarda también `my.ini`.
3. **Rescatar SQL:** con MySQL detenido, crea el respaldo y otra copia de trabajo.
   Inicia exclusivamente esa copia, en localhost:3307, con recuperación InnoDB
   nivel 1, y exporta todas las bases a `bases-recuperadas.sql`.

Los resultados quedan en una carpeta nueva dentro de `C:\Respaldos-MySQL`.
Mantener MySQL detenido durante la copia; no pulsar Start en XAMPP mientras trabaja.
La herramienta no borra archivos originales, no cambia `my.ini` y no reemplaza
la base activa. Una copia física de una base dañada preserva sus archivos, pero
no demuestra que estén sanos. Un SQL exportado necesita validación de datos.

Si root tiene contraseña, ejecutar en PowerShell:

```powershell
.\scripts\Recuperar-MySQL.ps1 -Accion RescatarSQL -PedirClave
```

Se pedirá la contraseña al exportar y al cerrar la instancia auxiliar; no se
guarda en el script. Parámetros disponibles: `-Xampp`, `-Destino`,
`-PuertoRescate`, `-Usuario`. El destino debe estar fuera de `mysql` y `htdocs`.
Ejecutar con una cuenta que pueda leer los datos y escribir en el destino.

## Después del rescate

Importar el SQL en una instalación **separada y limpia**, compatible con la misma
versión de MariaDB (esta instalación utiliza 10.4.32). El archivo incluye todas
las bases y tablas del sistema: no importarlo sobre una instalación en uso.
Verificar tablas, cantidades de registros, usuarios y funcionamiento de SisDiT
antes de planear el reemplazo. Conservar originales y respaldo mientras se valida.

Si la exportación falla, el archivo se llama `rescate-incompleto.sql`. No usarlo
como respaldo completo. Consultar `rescate.log`: el script no aumenta la fuerza
de recuperación ni intenta reparar automáticamente otros tipos de corrupción.
No resuelve todos los errores de arranque; un puerto ocupado o permisos incorrectos
requieren corregir esa causa.

## Referencias

- [MariaDB: cuando el servidor no inicia](https://mariadb.com/docs/server/server-management/starting-and-stopping-mariadb/what-to-do-if-mariadb-doesnt-start)
- [Modos de recuperación InnoDB](https://mariadb.com/docs/server/server-usage/storage-engines/innodb/innodb-troubleshooting/innodb-recovery-modes): permiten intentar extraer datos; no reparan la corrupción.
- [Redo log de InnoDB](https://mariadb.com/docs/server/server-usage/storage-engines/innodb/innodb-redo-log): no borrar ni intercambiar registros para forzar el arranque.
