// script.js

document.getElementById('formulario-factura').addEventListener('submit', function(event) {
  event.preventDefault();

  var formData = new FormData(this);

  fetch('procesar-factura.php', {
      method: 'POST',
      body: formData
  })
  .then(response => response.json())
  .then(data => {
      // Mostrar resultados en la tabla
      var tablaBody = document.getElementById('tabla-body');
      tablaBody.innerHTML = ''; // Limpiar resultados anteriores

      if (data.error) {
          alert('Error: ' + data.error);
      } else {
          var row = document.createElement('tr');
          var cell1 = document.createElement('td');
          var cell2 = document.createElement('td');
          cell1.textContent = data.supplierTaxId;
          cell2.textContent = data.supplierName;
          row.appendChild(cell1);
          row.appendChild(cell2);
          tablaBody.appendChild(row);
          document.getElementById('contenedor-resultados').style.display = 'block';
      }
  })
  .catch(error => {
      console.error('Error:', error);
      alert('Hubo un problema al procesar la factura.');
  });
});
