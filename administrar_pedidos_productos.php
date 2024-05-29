<?php
session_start();
include 'config/database.php';

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_rol'] != 1 && $_SESSION['usuario_rol'] != 2)) {
    header('Location: login.php');
    exit();
}

// Función para eliminar un pedido
if (isset($_GET['eliminar_pedido'])) {
    $pedido_id = intval($_GET['eliminar_pedido']); // Asegúrate de que $pedido_id sea un entero

    // Primero, eliminar los registros asociados en pedidos_productos
    $eliminar_pedidos_productos_sql = "DELETE FROM pedidos_productos WHERE pedido_id = $pedido_id";
    if ($conn->query($eliminar_pedidos_productos_sql) === TRUE) {
        // Luego, eliminar el pedido
        $eliminar_pedido_sql = "DELETE FROM pedidos WHERE id = $pedido_id";
        if ($conn->query($eliminar_pedido_sql) === TRUE) {
            $msg = "Pedido eliminado correctamente.";
        } else {
            $error_msg = "Error al eliminar el pedido: " . $conn->error;
        }
    } else {
        $error_msg = "Error al eliminar los productos del pedido: " . $conn->error;
    }
}

// Consulta para obtener todos los pedidos con información adicional
$sql = "
SELECT pedidos.id AS pedido_id, pedidos.fecha_creacion, usuarios.nombre AS usuario_nombre, productos.nombre AS producto_nombre, productos.precio AS producto_precio
FROM pedidos
JOIN usuarios ON pedidos.usuario_id = usuarios.id
JOIN pedidos_productos ON pedidos.id = pedidos_productos.pedido_id
JOIN productos ON pedidos_productos.producto_id = productos.id
ORDER BY pedidos.fecha_creacion DESC
";

$result = $conn->query($sql);

// Verificar si se recuperaron los pedidos correctamente
if (!$result) {
    die("Error al recuperar los pedidos: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Pedidos - PC-XPRESS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Estilo general para el cuerpo del documento */
        body {
            font-family: 'Oswald', sans-serif;
        }

        /* Estilos para el encabezado */
        header {
            background-color: #333;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 2em;
            color: white;
        }

        nav {
            position: absolute;
            right: 20px;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            background-color: #555;
            border-radius: 4px;
        }

        nav a:hover {
            background-color: #FF9999;
        }

        main {
            padding: 20px;
        }

        .pedidos {
            display: flex;
            flex-wrap: wrap;
        }

        .pedido {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            width: calc(33% - 40px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .pedido h3 {
            margin-top: 0;
        }

        .pedido p {
            margin: 5px 0;
        }

        .pedido .button {
            display: inline-block;
            padding: 6px 10px;
            /* Ajuste de tamaño del botón */
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
            border: none;
            /* Quita el borde */
            cursor: pointer;
            font-size: 14px;
            /* Tamaño de la fuente */
            line-height: 1;
            /* Ajusta el espaciado entre líneas */
            text-align: center;
            /* Centrar el texto */
            width: auto;
            /* Ajustar automáticamente el ancho */
        }


        .pedido .button:hover {
            background-color: #FF9999;
        }

        .mensaje {
            margin: 10px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-left: 4px solid #4CAF50;
        }

        .error-mensaje {
            margin: 10px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-left: 4px solid #f44336;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: white;
        }
    </style>
</head>

<body>
    <header>
        <h1>ADMINISTRAR PEDIDOS</h1>
        <nav>
            <a href="index.php">Volver al Inicio</a>
        </nav>
    </header>
    <main>
        <h2>Pedidos</h2>

        <?php if (isset($msg)): ?>
            <div class="mensaje"><?php echo $msg; ?></div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="error-mensaje"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="pedidos">
            <?php
            $current_pedido_id = null;
            while ($row = $result->fetch_assoc()):
                if ($current_pedido_id !== $row['pedido_id']):
                    if ($current_pedido_id !== null):
                        echo '<a href="administrar_pedidos_productos.php?eliminar_pedido=' . htmlspecialchars($current_pedido_id) . '" class="button" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este pedido?\');">Eliminar Pedido</a>';
                        echo '</div>';
                    endif;
                    $current_pedido_id = $row['pedido_id'];
                    ?>
                    <div class="pedido">
                        <h3>Pedido ID: <?php echo htmlspecialchars($row['pedido_id']); ?></h3>
                        <p>Fecha de Creación: <?php echo htmlspecialchars($row['fecha_creacion']); ?></p>
                        <p>Usuario: <?php echo htmlspecialchars($row['usuario_nombre']); ?></p>
                        <p><strong>Productos:</strong></p>
                    <?php endif; ?>
                    <p><?php echo htmlspecialchars($row['producto_nombre']); ?> -
                        $<?php echo htmlspecialchars($row['producto_precio']); ?></p>
                <?php endwhile;
            if ($current_pedido_id !== null):
                echo '<a href="administrar_pedidos_productos.php?eliminar_pedido=' . htmlspecialchars($current_pedido_id) . '" class="button" onclick="return confirm(\'¿Estás seguro de que deseas eliminar este pedido?\');">Eliminar Pedido</a>';
                echo '</div>';
            endif;
            ?>
            </div>
    </main>
    <footer>
        <p>© 2025 PC-XPRESS</p>
    </footer>
</body>

</html>