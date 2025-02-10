<?php

$mesero_id = isset($_GET['mesero_id']) ? $_GET['mesero_id'] : null;

// Validar que el mesero_id sea válido
if (!$mesero_id) {
    die("Mesero no seleccionado.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Selección de Mesa</title>
</head>
<body>
    <h1>Selecciona una Mesa</h1>
    <div class="mesas-container">
        <form action="guardar_comanda.php" method="POST">
            <input type="hidden" name="mesero_id" value="<?php echo htmlspecialchars($mesero_id); ?>">
            <button type="submit" id="continuar" disabled>Continuar</button>

            <div class="mesas-botones" id="mesas-botones">
                <!-- Botones de las mesas cargados dinámicamente -->
            </div>
        </form>
        <div id="mesa-detalle" class="mesa-detalle">
    <h2>Detalles de la Mesa</h2>
    <p><strong>Estado:</strong> <span id="detalle-estado">N/A</span></p>
    <p><strong>Espacio:</strong> <span id="detalle-espacio">N/A</span></p>
    <p><strong>Descripción:</strong> <span id="detalle-descripcion">N/A</span></p>
    </div>
    </div>  



    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    // Cargar mesas disponibles
    async function loadMesas() {
        try {
            const response = await axios.get('../controllers/getMesasController.php');
            const container = document.getElementById('mesas-botones');
            const continuarBtn = document.getElementById('continuar');
            const detalleEstado = document.getElementById('detalle-estado');
            const detalleEspacio = document.getElementById('detalle-espacio');
            const detalleDescripcion = document.getElementById('detalle-descripcion');

            response.data.forEach(mesa => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `mesa-btn ${mesa.estado.toLowerCase()}`; // Clase según el estado
                button.textContent = `Mesa ${mesa.id_mesa}`;
                
                // Deshabilitar botones según el estado
                if (mesa.estado === 'Reservada' || mesa.estado === 'Ocupada') {
                    button.disabled = true;
                }

                // Evento de selección
                button.addEventListener('click', () => {
                    document.querySelectorAll('.mesa-btn').forEach(btn => btn.classList.remove('selected'));
                    button.classList.add('selected');
                    continuarBtn.disabled = false;

                    // Crear input oculto
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'mesa';
                    input.value = mesa.id_mesa;
                    container.querySelector('input[name="mesa"]')?.remove();
                    container.appendChild(input);

                    // Actualizar detalles de la mesa
                    detalleEstado.textContent = mesa.estado;
                    detalleEspacio.textContent = mesa.espacio;
                    detalleDescripcion.textContent = mesa.descripcion;
                });
                container.appendChild(button);
            });
        } catch (error) {
            console.error('Error al cargar mesas:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', loadMesas);
</script>


</body>
</html>
