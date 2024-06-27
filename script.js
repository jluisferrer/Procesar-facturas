document.getElementById('archivo-factura').addEventListener('change', function (event) {
    event.preventDefault();  // Previene el envío del formulario si se desencadena por otro evento.

    let formData = new FormData(document.getElementById('formulario-factura'));

    fetch('procesar-factura.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            // Mostrar resultados en la tabla
            let tablaBody = document.getElementById('tabla-body');
            tablaBody.innerHTML = ''; // Limpiar resultados anteriores

            if (data.error) {
                alert('Error: ' + data.error);
            } else {
                let row = document.createElement('tr');
                let cell1 = document.createElement('td');
                let cell2 = document.createElement('td');
                let cell3 = document.createElement('td');
                let cell4 = document.createElement('td');
                let cell5 = document.createElement('td');
                let cell6 = document.createElement('td');
                let cell7 = document.createElement('td');

                cell1.textContent = data.supplierCompanyRegistrations;
                cell2.textContent = data.supplierName;
                cell3.textContent = data.invoiceNumber;
                cell4.textContent = data.totalAmount;
                cell5.textContent = data.totalTax;
                cell6.textContent = data.numPages;
                cell7.textContent = data.hash;

                row.appendChild(cell1);
                row.appendChild(cell2);
                row.appendChild(cell3);
                row.appendChild(cell4);
                row.appendChild(cell5);
                row.appendChild(cell6);
                row.appendChild(cell7);
                tablaBody.appendChild(row);
                document.getElementById('contenedor-resultados').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un problema al procesar la factura.');
        });
});
