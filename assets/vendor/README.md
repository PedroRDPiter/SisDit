# Bibliotecas del editor de firma

Archivos locales, sin enviar documentos ni firmas a servicios externos:

- pdf-lib 1.17.1: `pdf-lib/pdf-lib.min.js` y licencia MIT. Fuente: https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js
- PDF.js (pdfjs-dist) 6.3.289: `pdfjs/pdf.mjs`, `pdfjs/pdf.worker.mjs` y licencia Apache-2.0. Fuente: https://unpkg.com/pdfjs-dist@6.3.289/build/. Las fuentes estándar y los recursos WASM, con sus licencias, proceden de https://registry.npmjs.org/pdfjs-dist/-/pdfjs-dist-6.3.289.tgz.

Documentación consultada: https://pdf-lib.js.org/docs/api/classes/pdfpage y https://mozilla.github.io/pdf.js/examples/

El editor incrusta la imagen en el PDF. La firma visible no incorpora certificados de firma electrónica. El servidor conserva original, PDF firmado, hash SHA-256, responsable y fecha.
