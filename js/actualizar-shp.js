document.getElementById('form-actualizar-shp')?.addEventListener('submit', async function(event) {
    event.preventDefault();
    const archivo = this.elements.shp.files[0];
    const resultado = document.getElementById('resultado-shp');
    const boton = this.querySelector('button[type="submit"]');

    resultado.className = 'alert alert-info mt-3';

    if (!archivo || !/\.shp$/i.test(archivo.name) || archivo.size > 30 * 1024 * 1024) {
        resultado.textContent = 'Selecciona un archivo .shp de hasta 30 MB.';
        return;
    }

    boton.disabled = true;
    this.setAttribute('aria-busy', 'true');
    resultado.textContent = 'Convirtiendo y actualizando polígonos. Espera a que termine el proceso.';

    try {
        const response = await fetch('php/actualizar_shp.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: new FormData(this)
        });
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'No se pudo actualizar la capa.');
        }

        resultado.className = 'alert alert-success mt-3';
        resultado.textContent = `${data.message} Polígonos: ${data.poligonos}. Claves recuperadas: ${data.claves_recuperadas}. Sin clave: ${data.sin_clave}. Geometrías vacías o degeneradas omitidas: ${data.omitidos}.`;
        this.reset();
    } catch (error) {
        resultado.className = 'alert alert-danger mt-3';
        resultado.textContent = error instanceof SyntaxError
            ? 'El servidor no pudo procesar el archivo. Recarga la página e intenta nuevamente.'
            : error.message;
    } finally {
        boton.disabled = false;
        this.removeAttribute('aria-busy');
    }
});
