import * as pdfjs from '../assets/vendor/pdfjs/pdf.mjs';
pdfjs.GlobalWorkerOptions.workerSrc = new URL('../assets/vendor/pdfjs/pdf.worker.mjs', import.meta.url).href;

const MAX_PDF = 10 * 1024 * 1024;
const el = id => document.getElementById(id);

export class EditorFirma {
    constructor(onChange) {
        this.onChange = onChange;
        this.generation = 0;
        this.draw = el('firmaDibujo');
        this.ctx = this.draw.getContext('2d');
        el('firmaPdf').addEventListener('change', () => this.run(() => this.cargarPdf(el('firmaPdf').files[0])));
        el('firmaImagen').addEventListener('change', () => this.run(() => this.cargarImagen(el('firmaImagen').files[0])));
        el('firmaPagina').addEventListener('change', () => this.run(() => this.renderPagina()));
        ['firmaTamano', 'firmaHorizontal', 'firmaVertical'].forEach(id => el(id).addEventListener('input', () => this.posicionar()));
        el('usarFirmaDibujo').addEventListener('click', () => this.run(() => this.usarCanvas(this.draw)));
        el('limpiarFirmaDibujo').addEventListener('click', () => {
            this.ctx.clearRect(0, 0, this.draw.width, this.draw.height);
            this.imagen = null;
            el('firmaSello').hidden = true;
            this.invalidar();
        });
        el('prepararFirmaPdf').addEventListener('click', () => this.run(() => this.preparar()));
        this.configurarDibujo();
        this.configurarArrastre();
        this.reset();
    }
    async run(action) {
        try { await action(); } catch (error) {
            this.mensaje(error.message || 'No se pudo procesar el documento.', true);
        }
    }
    mensaje(texto, error = false) {
        el('firmaEstado').textContent = texto;
        el('firmaEstado').className = `alert small ${error ? 'alert-danger' : 'alert-light border'}`;
    }
    invalidar() {
        this.signed = null;
        if (el('firmaVacia')) {
            const visible = !!this.pdf && !!this.viewport && !this.rendering;
            el('firmaVacia').hidden = visible;
            el('firmaPapel').closest('.firma-papel-contenedor').hidden = !visible;
        }
        if (this.url) URL.revokeObjectURL(this.url);
        this.url = null;
        el('descargarFirmaPdf').hidden = true;
        el('descargarFirmaPdf').removeAttribute('href');
        el('firmaPreparada').hidden = true;
        el('prepararFirmaPdf').disabled = !this.pdf || !this.imagen || this.rendering;
        this.onChange(false);
    }
    reset() {
        this.generation++;
        this.renderTask?.cancel();
        this.loadingTask?.destroy();
        this.loadingTask = null;
        this.pdf = null;
        this.original = null;
        this.imagen = null;
        this.viewport = null;
        this.rendering = false;
        this.ctx.clearRect(0, 0, this.draw.width, this.draw.height);
        el('firmaPdf').value = '';
        el('firmaImagen').value = '';
        el('firmaPagina').replaceChildren(new Option('Carga un PDF', ''));
        el('firmaPagina').disabled = true;
        el('firmaPaginaCanvas').width = 0;
        el('firmaPaginaCanvas').height = 0;
        el('firmaSello').hidden = true;
        el('firmaTamano').value = 25;
        el('firmaHorizontal').value = 50;
        el('firmaVertical').value = 80;
        this.invalidar();
        this.mensaje('Carga un PDF para ver la página.');
    }
    async cargarPdf(file) {
        if (!file) return;
        // Invalidar la selección previa antes de procesar otro documento.
        this.generation++;
        const generation = this.generation;
        this.renderTask?.cancel();
        this.loadingTask?.destroy();
        this.loadingTask = null;
        this.pdf = null;
        this.original = null;
        this.invalidar();
        el('firmaSello').hidden = true;
        el('firmaPaginaCanvas').width = 0;
        el('firmaPaginaCanvas').height = 0;
        if (file.size > MAX_PDF || file.size === 0) throw new Error('El PDF debe contener datos y pesar como máximo 10 MiB.');
        this.mensaje('Preparando la vista previa del PDF…');
        const bytes = new Uint8Array(await file.arrayBuffer());
        if (generation !== this.generation) return;
        try {
            await window.PDFLib.PDFDocument.load(bytes);
            const task = pdfjs.getDocument({data: bytes.slice(), isEvalSupported: false,
                standardFontDataUrl: new URL('../assets/vendor/pdfjs/standard_fonts/', import.meta.url).href,
                wasmUrl: new URL('../assets/vendor/pdfjs/wasm/', import.meta.url).href});
            this.loadingTask = task;
            task.onPassword = () => { task.destroy(); };
            const pdf = await task.promise;
            if (generation !== this.generation) { task.destroy(); return; }
            this.pdf = pdf;
            this.original = bytes;
            el('firmaPagina').replaceChildren(...Array.from({length: pdf.numPages}, (_, i) => new Option(`Página ${i + 1} de ${pdf.numPages}`, i + 1)));
            el('firmaPagina').disabled = false;
            await this.renderPagina();
        } catch (error) {
            if (generation === this.generation) {
                this.pdf = null;
                this.original = null;
                this.invalidar();
                throw new Error('No se pudo abrir el PDF. Usa un documento válido, sin contraseña.');
            }
        }
    }
    async renderPagina() {
        if (!this.pdf) return;
        const generation = ++this.generation;
        this.rendering = true;
        this.invalidar();
        this.renderTask?.cancel();
        if (this.renderTask) { try { await this.renderTask.promise; } catch (_) {} }
        const page = await this.pdf.getPage(Number(el('firmaPagina').value));
        if (generation !== this.generation) return;
        const size = page.getViewport({scale: 1});
        this.viewport = page.getViewport({scale: Math.min(1.5, 900 / size.width)});
        const canvas = el('firmaPaginaCanvas');
        canvas.width = Math.ceil(this.viewport.width);
        canvas.height = Math.ceil(this.viewport.height);
        this.renderTask = page.render({canvasContext: canvas.getContext('2d'), viewport: this.viewport});
        try { await this.renderTask.promise; } catch (error) { if (error.name !== 'RenderingCancelledException') throw error; }
        if (generation !== this.generation) return;
        this.rendering = false;
        this.posicionar();
        this.mensaje('Vista previa lista. Ajusta la firma y prepara el PDF para revisarlo.');
    }
    async cargarImagen(file) {
        if (!file) return;
        this.imagen = null;
        this.invalidar();
        el('firmaSello').hidden = true;
        if (!['image/png', 'image/jpeg'].includes(file.type) || file.size > 2 * 1024 * 1024) throw new Error('La firma debe ser PNG o JPG de hasta 2 MiB.');
        const generation = this.generation;
        const bitmap = await createImageBitmap(file);
        if (generation !== this.generation) { bitmap.close(); return; }
        const canvas = document.createElement('canvas');
        const scale = Math.min(1, 1200 / Math.max(bitmap.width, bitmap.height));
        canvas.width = Math.max(1, Math.round(bitmap.width * scale));
        canvas.height = Math.max(1, Math.round(bitmap.height * scale));
        canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
        bitmap.close();
        this.usarCanvas(canvas);
    }
    usarCanvas(canvas) {
        const ctx = canvas.getContext('2d');
        const image = ctx.getImageData(0, 0, canvas.width, canvas.height);
        let left = canvas.width, right = -1, top = canvas.height, bottom = -1;
        for (let y = 0; y < canvas.height; y++) for (let x = 0; x < canvas.width; x++) {
            const i = (y * canvas.width + x) * 4;
            if (image.data[i] > 245 && image.data[i + 1] > 245 && image.data[i + 2] > 245) image.data[i + 3] = 0;
            if (image.data[i + 3] > 25) { left = Math.min(left, x); right = Math.max(right, x); top = Math.min(top, y); bottom = Math.max(bottom, y); }
        }
        if (right - left < 8 || bottom - top < 3) throw new Error('Dibuja o carga una firma visible antes de continuar.');
        const clean = document.createElement('canvas');
        clean.width = canvas.width; clean.height = canvas.height;
        clean.getContext('2d').putImageData(image, 0, 0);
        const cropped = document.createElement('canvas');
        cropped.width = right - left + 9; cropped.height = bottom - top + 9;
        cropped.getContext('2d').drawImage(clean, left, top, right - left + 1, bottom - top + 1, 4, 4, right - left + 1, bottom - top + 1);
        this.imagen = {data: cropped.toDataURL('image/png'), ratio: cropped.height / cropped.width};
        el('firmaSello').src = this.imagen.data;
        this.posicionar();
        this.mensaje('Firma lista. Colócala en el espacio del oficio.');
    }
    posicionar() {
        this.invalidar();
        if (!this.viewport || !this.pdf || !this.imagen || this.rendering) return;
        const page = this.viewport;
        let width = page.width * Number(el('firmaTamano').value) / 100;
        let height = width * this.imagen.ratio;
        if (height > page.height * .45) { height = page.height * .45; width = height / this.imagen.ratio; }
        const x = (page.width - width) * Number(el('firmaHorizontal').value) / 100;
        const y = (page.height - height) * Number(el('firmaVertical').value) / 100;
        this.box = {x, y, width, height};
        Object.assign(el('firmaSello').style, {left: `${x / page.width * 100}%`, top: `${y / page.height * 100}%`, width: `${width / page.width * 100}%`, height: `${height / page.height * 100}%`});
        el('firmaSello').hidden = false;
    }
    async preparar() {
        if (!this.original || !this.imagen || this.rendering) throw new Error('Carga el PDF y coloca una firma primero.');
        const generation = this.generation;
        const original = this.original.slice();
        const imageData = this.imagen.data;
        const box = {...this.box};
        const viewport = this.viewport;
        const pageNumber = Number(el('firmaPagina').value);
        const snapshot = JSON.stringify({box, imageData, pageNumber});
        el('prepararFirmaPdf').disabled = true;
        this.mensaje('Incorporando la firma al PDF…');
        try {
            const {PDFDocument, degrees} = window.PDFLib;
            const pdf = await PDFDocument.load(original);
            const image = await pdf.embedPng(imageData);
            const page = pdf.getPage(pageNumber - 1);
            const [x, y] = viewport.convertToPdfPoint(box.x, box.y + box.height);
            page.drawImage(image, {x, y, width: box.width / viewport.scale, height: box.height / viewport.scale, rotate: degrees(viewport.rotation)});
            const bytes = await pdf.save();
            if (bytes.length > MAX_PDF) throw new Error('El PDF firmado supera 10 MiB. Utiliza un original más pequeño.');
            if (generation !== this.generation || snapshot !== JSON.stringify({box: this.box, imageData: this.imagen?.data, pageNumber: Number(el('firmaPagina').value)})) return;
            this.invalidar();
            this.signed = new Blob([bytes], {type: 'application/pdf'});
            this.url = URL.createObjectURL(this.signed);
            el('descargarFirmaPdf').href = this.url;
            el('descargarFirmaPdf').hidden = false;
            el('firmaPreparada').hidden = false;
            this.onChange(true);
            this.mensaje('PDF firmado preparado. Abre «Revisar PDF firmado» y confirma para guardarlo en el expediente.');
        } finally { el('prepararFirmaPdf').disabled = !this.pdf || !this.imagen || this.rendering; }
    }
    configurarDibujo() {
        let drawing = false;
        const point = event => {
            const r = this.draw.getBoundingClientRect();
            return [(event.clientX - r.left) * this.draw.width / r.width, (event.clientY - r.top) * this.draw.height / r.height];
        };
        this.draw.addEventListener('pointerdown', event => {
            drawing = true;
            this.draw.setPointerCapture(event.pointerId);
            this.ctx.beginPath(); this.ctx.moveTo(...point(event));
            this.ctx.strokeStyle = '#132b55'; this.ctx.lineWidth = 3; this.ctx.lineCap = 'round'; this.ctx.lineJoin = 'round';
            this.invalidar();
        });
        this.draw.addEventListener('pointermove', event => { if (drawing) { this.ctx.lineTo(...point(event)); this.ctx.stroke(); } });
        const end = () => { drawing = false; };
        this.draw.addEventListener('pointerup', end); this.draw.addEventListener('pointercancel', end);
    }
    configurarArrastre() {
        const stamp = el('firmaSello');
        let drag = null;
        stamp.addEventListener('pointerdown', event => {
            if (!this.box) return;
            const r = stamp.getBoundingClientRect();
            drag = {x: event.clientX - r.left, y: event.clientY - r.top};
            stamp.setPointerCapture(event.pointerId);
        });
        stamp.addEventListener('pointermove', event => {
            if (!drag || !this.viewport) return;
            const rect = el('firmaPaginaCanvas').getBoundingClientRect();
            const scale = rect.width / this.viewport.width;
            const x = (event.clientX - rect.left - drag.x) / scale;
            const y = (event.clientY - rect.top - drag.y) / scale;
            el('firmaHorizontal').value = Math.max(0, Math.min(100, x / (this.viewport.width - this.box.width) * 100));
            el('firmaVertical').value = Math.max(0, Math.min(100, y / (this.viewport.height - this.box.height) * 100));
            this.posicionar();
        });
        stamp.addEventListener('pointerup', () => { drag = null; });
        stamp.addEventListener('pointercancel', () => { drag = null; });
    }
    adjuntar(formData) {
        if (!this.signed || !this.original) throw new Error('Prepara y revisa el PDF firmado antes de guardar.');
        formData.set('documento_firmado', this.signed, 'oficio_firmado.pdf');
        formData.set('documento_original', new Blob([this.original], {type: 'application/pdf'}), 'oficio_original.pdf');
        formData.set('pagina_firma', el('firmaPagina').value);
    }
}
