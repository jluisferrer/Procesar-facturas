const archivoFactura = document.getElementById('archivo-factura');
const btnExtraer = document.getElementById('btn-extraer');
const contenedorResultados = document.getElementById('contenedor-resultados');
const tablaResultados = document.getElementById('tabla-resultados');

btnExtraer.addEventListener('click', () => {
    const file = archivoFactura.files[0];
    if (file && file.type === 'application/pdf') {
        contenedorResultados.style.display = 'none';
        tablaResultados.innerHTML = '';

        const formData = new FormData();
        formData.append('file', file);

        fetch('procesar-factura.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.datos) {
                    mostrarResultados(data.datos);
                } else {
                    alert('Error al procesar la factura');
                }
            })
            .catch(error => console.error(error));
    } else {
        alert('Seleccione un archivo PDF válido');
    }
});

function mostrarResultados(datos) {
    contenedorResultados.style.display = 'block';

    for (const factura of datos) {
        const fila = tablaResultados.insertRow();
        const numeroFactura = fila.insertCell();
        const nifEmpresa = fila.insertCell();
        const nombreEmpresa = fila.insertCell();
        const importeTotal = fila.insertCell();
        const iva = fila.insertCell();

        numeroFactura.textContent = factura.numeroFactura;
        nifEmpresa.textContent = factura.nifEmpresa;
        nombreEmpresa.textContent = factura.nombreEmpresa;
        importeTotal.textContent = factura.importeTotal;
        iva.textContent = factura.iva;
    }
}
