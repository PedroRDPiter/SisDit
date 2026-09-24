// Ejecutar únicamente contra el servidor conectado a tests/fixture_oficios.php.
const { chromium } = require(process.env.SISDIT_PLAYWRIGHT || 'playwright');
const { PDFDocument, StandardFonts, degrees } = require('../assets/vendor/pdf-lib/pdf-lib.min.js');
const assert = require('node:assert/strict');
const path = require('node:path');
const fs = require('node:fs');
const baseURL = process.env.SISDIT_TEST_URL || 'http://127.0.0.1:8097';
if (!/^http:\/\/127\.0\.0\.1:8097$/.test(baseURL)) {
    throw Error('Usa el servidor aislado de pruebas en 127.0.0.1:8097.');
}
const artifacts = path.join(require('node:os').tmpdir(), 'sisdit-oficios-tests');
fs.mkdirSync(artifacts, { recursive: true });
async function login(context, rol) {
    const response = await context.request.get('/acceso.php');
    const html = await response.text();
    const csrf = html.match(/name="csrf_token"[^>]*value="([^"]+)"/)?.[1];
    assert.ok(csrf, 'Token de acceso');
    const result = await context.request.post('/php/login.php', {
        form: {
            correo: `${rol}@pruebas.example`,
            password: 'Prueba-Oficios-2026!',
            csrf_token: csrf
        }
    });
    assert.equal(result.status(), 200);
    const destino = await result.text();
    return destino.match(/name="csrf_token"[^>]*value="([^"]+)"/)?.[1] || csrf;
}
(async () => {
    const browser = await chromium.launch({ channel: 'chrome', headless: true });
    try {
        const context = await browser.newContext({
            baseURL,
            ignoreHTTPSErrors: true,
            viewport: { width: 1440, height: 1000 }
        });
        const csrf = await login(context, 'administrador');
        const page = await context.newPage();
        page.setDefaultTimeout(20000);
        const errors = [];
        page.on('pageerror', error => {
            errors.push(error.message);
            console.log('PAGE ERROR:', error.stack);
        });
        page.on('console', message => {
            if (message.type() === 'error') console.log('BROWSER:', message.text());
        });
        await page.goto('/DashAdmin.php', {waitUntil: 'networkidle'});
        await page.waitForSelector('#tablaOficiosAdmin_wrapper', {timeout: 30000});
        await page.screenshot({path: path.join(artifacts, 'bandeja.png'), fullPage: false});
        const invalidCsrf = await context.request.post('/php/admin_oficios.php', {form: {id: '1', accion: 'firmar'}});
        assert.equal(invalidCsrf.status(), 403);
        const closeEarly = await context.request.post('/php/admin_oficios.php', {form: {id: '1', accion: 'cerrar', estado_esperado: 'Pendiente por firmar', confirmar: '1', csrf_token: csrf}});
        assert.equal(closeEarly.status(), 409);
        const missing = await context.request.post('/php/admin_oficios.php', {form: {id: '3', accion: 'cerrar', estado_esperado: 'Firmado', confirmar: '1', csrf_token: csrf}});
        assert.equal(missing.status(), 422);
        const fake = await context.request.post('/php/admin_oficios.php', {multipart: {id: '3', accion: 'cerrar', estado_esperado: 'Firmado', confirmar: '1', csrf_token: csrf, documento_firmado: {name: 'falso.pdf', mimeType: 'application/pdf', buffer: Buffer.from('no es PDF')}}});
        assert.equal(fake.status(), 422);
        console.log('OK: CSRF, transición inválida, adjunto obligatorio y MIME falso.');

        const pdf = await PDFDocument.create();
        const font = await pdf.embedFont(StandardFonts.Helvetica);
        pdf.addPage([612, 792]).drawText('OFICIO FICTICIO - PRUEBA DE FIRMA', {x: 40, y: 700, font, size: 17});
        const rotated = pdf.addPage([612, 792]);
        rotated.setRotation(degrees(90));
        rotated.drawText('PAGINA DOS - ROTACION 90', {x: 40, y: 500, font, size: 14});
        const original = Buffer.from(await pdf.save());
        const storedDir = path.join(__dirname, '..', '.private', 'oficios');
        const beforeFiles = fs.existsSync(storedDir) ? fs.readdirSync(storedDir).sort() : [];
        const incomplete = await context.request.post('/php/admin_oficios.php', {
            multipart: {
                id: '5',
                accion: 'firmar',
                estado_esperado: 'Pendiente por firmar',
                confirmar: '1',
                csrf_token: csrf,
                documento_firmado: {
                    name: 'firmado.pdf',
                    mimeType: 'application/pdf',
                    buffer: original
                }
            }
        });
        assert.equal(incomplete.status(), 422);
        assert.deepEqual(fs.readdirSync(storedDir).filter(name => name !== '.htaccess').sort(), beforeFiles.filter(name => name !== '.htaccess'));
        assert.equal((await (await context.request.get('/php/admin_oficios.php?id=5')).json()).tramite.estatus, 'Pendiente por firmar');
        console.log('OK: un fallo intermedio revierte el estado y elimina el archivo parcial.');
        await page.locator('.abrir-oficio[data-id="1"]').click();
        await page.waitForSelector('#oficioContenido:not([hidden])');
        await page.locator('#oficioContinuar').click();
        await page.locator('#firmaPdf').setInputFiles({name: 'oficio_prueba.pdf', mimeType: 'application/pdf', buffer: original});
        await page.waitForFunction(() => document.getElementById('firmaPagina').options.length === 2 && !document.getElementById('firmaPagina').disabled);
        await page.locator('#firmaPagina').selectOption('2');
        await page.waitForFunction(() => document.getElementById('firmaEstado').textContent.startsWith('Vista previa lista'));
        const canvas = page.locator('#firmaDibujo');
        await canvas.scrollIntoViewIfNeeded();
        const box = await canvas.boundingBox();
        await page.mouse.move(box.x + 30, box.y + 40);
        await page.mouse.down();
        for (let i = 0; i < 15; i++) await page.mouse.move(box.x + 30 + i * 12, box.y + 40 + Math.sin(i) * 20);
        await page.mouse.up();
        await page.locator('#usarFirmaDibujo').click();
        await page.locator('#prepararFirmaPdf').click();
        await page.waitForSelector('#firmaPreparada:not([hidden])');
        const firmaPng = await page.locator('#firmaSello').getAttribute('src');
        assert.equal(await page.locator('#guardarOficio').isEnabled(), true);
        const finalBytes = await page.evaluate(async () => Array.from(new Uint8Array(await (await fetch(document.getElementById('descargarFirmaPdf').href)).arrayBuffer())));
        const finalPdf = await PDFDocument.load(Uint8Array.from(finalBytes));
        assert.equal(finalPdf.getPageCount(), 2);
        assert.equal(finalPdf.getPage(1).getRotation().angle, 90);
        assert.ok(finalPdf.getPage(1).node.Resources().toString().includes('/Image'), 'La segunda página contiene la imagen de firma');
        const pixels = await page.evaluate(async bytes => {
            const pdfjs = await import('/assets/vendor/pdfjs/pdf.mjs');
            const task = pdfjs.getDocument({data: new Uint8Array(bytes), isEvalSupported: false});
            const doc = await task.promise;
            const pg = await doc.getPage(2), viewport = pg.getViewport({scale: 1});
            const canvas = document.createElement('canvas');
            canvas.width = viewport.width; canvas.height = viewport.height;
            await pg.render({canvasContext: canvas.getContext('2d'), viewport}).promise;
            const data = canvas.getContext('2d').getImageData(0,0,canvas.width,canvas.height).data;
            let blue = 0;
            for (let y = Math.floor(canvas.height * .6); y < canvas.height; y++) for (let x = Math.floor(canvas.width * .3); x < canvas.width * .7; x++) {
                const i = (y * canvas.width + x)*4;
                if (data[i+2] > data[i]+20 && data[i+2] > data[i+1]+15 && data[i] < 100) blue++;
            }
            await task.destroy();
            return blue;
        }, finalBytes);
        assert.ok(pixels > 30, 'La firma azul aparece en la posición visual esperada del PDF rotado');
        fs.writeFileSync(path.join(artifacts, 'firmado-prueba.pdf'), Buffer.from(finalBytes));
        await page.screenshot({path: path.join(artifacts, 'editor-firma.png')});
        await page.locator('#oficioConfirmacion').check();
        const savedPromise = page.waitForResponse(response => response.url().endsWith('/php/admin_oficios.php') && response.request().method() === 'POST');
        await page.locator('#guardarOficio').click();
        const saved = await savedPromise;
        const savedBody = await saved.json();
        assert.equal(savedBody.success, true, JSON.stringify(savedBody));
        const signed = await (await context.request.get('/php/admin_oficios.php?id=1')).json();
        assert.equal(signed.tramite.estatus, 'Firmado');
        assert.equal(signed.firma_digital_disponible, true);
        assert.equal(signed.tramite.observaciones, 'Observaciones previas conservadas');
        assert.equal(signed.historial[0].responsable, 'Administrador Pruebas');
        const finalDoc = signed.documentos.find(doc => doc.final);
        assert.ok(finalDoc);
        assert.equal((await context.request.get('/' + finalDoc.url)).status(), 200);
        const sibling = await (await context.request.get('/php/admin_oficios.php?id=2')).json();
        assert.equal(sibling.tramite.estatus, 'Pendiente por firmar');
        assert.ok(!sibling.tramite.folio_salida_numero);
        const repeat = await context.request.post('/php/admin_oficios.php', {form: {id: '1', accion: 'firmar', estado_esperado: 'Pendiente por firmar', confirmar: '1', csrf_token: csrf}});
        assert.equal(repeat.status(), 409);
        console.log('OK: firma visible en PDF de dos páginas, página rotada, original conservado, auditoría y aislamiento por ID.');
        await Promise.all([
            page.waitForEvent('load', {timeout: 20000}),
            page.locator('#modalOficioAdmin .modal-footer [data-bs-dismiss]').click()
        ]).catch(async error => {
            console.log(await page.evaluate(() => ({modal: document.getElementById('modalOficioAdmin').outerHTML.slice(0,500), message: document.getElementById('oficioMensaje').textContent})));
            await page.screenshot({path: path.join(artifacts, 'error-cierre-modal.png')});
            throw error;
        });
        await page.waitForSelector('#tablaOficiosAdmin_wrapper');
        await page.locator('.abrir-oficio[data-id="1"]').click();
        await page.waitForSelector('#oficioContenido:not([hidden])');
        await page.locator('#oficioContinuar').click();
        assert.equal(await page.locator('#oficioArchivoCierre').isVisible(), false);
        await page.locator('#oficioConfirmacion').check();
        const closedPromise = page.waitForResponse(response => response.url().endsWith('/php/admin_oficios.php') && response.request().method() === 'POST');
        await page.locator('#guardarOficio').click();
        const closed = await (await closedPromise).json();
        assert.equal(closed.estatus, 'Entregado y archivado', JSON.stringify(closed));
        const after = await (await context.request.get('/php/admin_oficios.php?id=1')).json();
        assert.ok(after.tramite.tiempo_salida);
        assert.equal(after.documentos.length, signed.documentos.length);
        console.log('OK: cierre digital reutiliza el PDF firmado y registra fecha.');
        await Promise.all([page.waitForEvent('load', {timeout: 20000}), page.locator('#modalOficioAdmin .modal-footer [data-bs-dismiss]').click()]);
        await page.waitForSelector('#tablaOficiosAdmin_wrapper');
        await page.locator('.oficios-filtro[data-estado="Pendiente por firmar"]').click();
        await page.locator('.abrir-oficio[data-id="2"]').click();
        await page.waitForSelector('#oficioContenido:not([hidden])');
        await page.locator('#oficioContinuar').click();
        await page.locator('#firmaPdf').setInputFiles({name: 'segundo.pdf', mimeType: 'application/pdf', buffer: original});
        await page.waitForFunction(() => document.getElementById('firmaEstado').textContent.startsWith('Vista previa lista'));
        await page.locator('#firmaImagen').setInputFiles({name: 'firma.png', mimeType: 'image/png', buffer: Buffer.from(firmaPng.split(',')[1], 'base64')});
        await page.waitForSelector('#firmaSello:not([hidden])');
        await page.locator('#prepararFirmaPdf').click();
        await page.waitForSelector('#firmaPreparada:not([hidden])');
        await page.locator('#firmaTamano').fill('30');
        assert.equal(await page.locator('#guardarOficio').isDisabled(), true, 'Cambiar la firma invalida el PDF preparado');
        await page.locator('#prepararFirmaPdf').click();
        await page.waitForSelector('#firmaPreparada:not([hidden])');
        await page.locator('#firmaPdf').setInputFiles({name: 'corrupto.pdf', mimeType: 'application/pdf', buffer: Buffer.from('no es PDF')});
        await page.waitForFunction(() => document.getElementById('firmaEstado').textContent.includes('No se pudo abrir'));
        assert.equal(await page.locator('#guardarOficio').isDisabled(), true, 'Un PDF inválido elimina la firma preparada previa');
        await page.locator('#modalOficioAdmin .modal-footer [data-bs-dismiss]').click();
        await page.waitForSelector('#modalOficioAdmin', {state: 'hidden'});
        console.log('OK: carga de firma PNG, invalidación al editar y rechazo de PDF corrupto.');
        const legacy = await context.request.post('/php/actualizarTramite.php', {form: {id: '5', folio: '100/2026', estatus: 'Firmado', verificador_nombre: 'VERIFICADOR DE PRUEBA', csrf_token: csrf}});
        const legacyBody = await legacy.json();
        assert.equal(legacyBody.success, true, JSON.stringify(legacyBody));
        const legacySaved = await (await context.request.get('/php/admin_oficios.php?id=5')).json();
        const stillPending = await (await context.request.get('/php/admin_oficios.php?id=2')).json();
        assert.notEqual(legacySaved.tramite.folio_salida_numero, after.tramite.folio_salida_numero);
        assert.equal(stillPending.tramite.estatus, 'Pendiente por firmar');
        assert.ok(!stillPending.tramite.folio_salida_numero);
        console.log('OK: el flujo existente comparte la secuencia y solo asigna folio al oficio firmado.');
        const anon = await browser.newContext({baseURL});
        assert.equal((await anon.request.get('/php/admin_oficios.php?id=1')).status(), 401);
        const other = await browser.newContext({baseURL});
        await login(other, 'usuario');
        assert.equal((await other.request.get('/php/admin_oficios.php?id=1')).status(), 403);
        assert.equal((await other.request.get('/' + finalDoc.url)).status(), 403);
        console.log('OK: acceso denegado sin sesión, por rol y a documentos ajenos.');
        await page.setViewportSize({width: 390, height: 844});
        await page.locator('#oficios-digitales').scrollIntoViewIfNeeded();
        assert.equal(await page.locator('#modalOficioAdmin').isVisible(), false);
        await page.screenshot({path: path.join(artifacts, 'bandeja-movil.png')});
        assert.equal(errors.length, 0, errors.join('\n'));
        console.log('OK: bandeja móvil y sin errores JavaScript. Artefactos:', artifacts);
    } finally {
        await browser.close();
    }
})().catch(error => {
    console.error(error);
    process.exit(1);
});
