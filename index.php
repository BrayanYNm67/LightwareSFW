<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Mesero</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <h1>Bienvenido a Lightware</h1>
    <h2>Selecciona un Mesero</h2>
    <form id="mesero-form" method="GET" action="seleccionM\select_mesas.php">
        <label for="mesero">Mesero:</label>
        <select id="mesero" name="mesero_id" required>
            <!-- Opciones cargadas dinámicamente -->
        </select>
        <button type="submit">Continuar</button>
    </form>

    <script>
        // Cargar meseros dinámicamente
        async function loadMeseros() {
            try {
                const response = await axios.get('controllers/getMeseroController.php');
                const select = document.getElementById('mesero');
                response.data.forEach(mesero => {
                    const option = document.createElement('option');
                    option.value = mesero.id_mesero;
                    option.textContent = mesero.nombre;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error al cargar meseros:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', loadMeseros);
    </script>
</body>
</html>
