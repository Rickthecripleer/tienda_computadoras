<?php
include 'config/database.php';

// Iniciar o reanudar la sesión
session_start();

// Si se envió un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $cantidad = $_POST['cantidad'];

    // Agregar el producto al carrito en la sesión
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = array();
    }

    if (isset($_SESSION['carrito'][$id])) {
        $_SESSION['carrito'][$id] += $cantidad;
    } else {
        $_SESSION['carrito'][$id] = $cantidad;
    }

    // Guardar el carrito en una cookie
    setcookie('carrito', serialize($_SESSION['carrito']), time() + (86400 * 30), '/'); // Cookie válida por 30 días
}

// Obtener el carrito desde la cookie si está disponible
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : (isset($_COOKIE['carrito']) ? unserialize($_COOKIE['carrito']) : array());
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <style>
        /* Estilos adicionales */
        body {
            font-family: 'Oswald', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            display: flex;
            justify-content: center;
            /* Centrar el título */
            align-items: center;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            position: relative;
            /* Para posicionar el nav a la izquierda */
        }

        header h1 {
            margin: 0;
        }

        nav {
            position: absolute;
            left: 20px;
            /* Posicionar el nav a la izquierda */
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: flex-start;
            /* Alinea los elementos a la izquierda */
        }

        nav ul li {
            margin-right: 20px;
        }

        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        main {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            /* Aumenta el espacio alrededor del contenido */
            background-color: #fff;
            /* Fondo blanco alrededor del contenido */
            border-radius: 5px;
            /* Bordes redondeados */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* Sombra suave */
            text-align: center;
            /* Centra el contenido */
        }

        h2 {
            margin-top: 20px;
            /* Aumenta el espacio superior */
        }

        .carrito {
            margin-top: 20px;
            /* Aumenta el espacio superior */
        }

        .carrito table {
            width: 100%;
            border-collapse: collapse;
        }

        .carrito th,
        .carrito td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .carrito th {
            background-color: #f2f2f2;
        }

        .carrito th:first-child,
        .carrito td:first-child {
            text-align: left;
        }

        .carrito th:last-child,
        .carrito td:last-child {
            text-align: right;
        }

        .carrito a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .carrito a:hover {
            background-color: #45a049;
        }

        .carrito p {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <header>
        <h1>PC-XPRESS</h1>
        <nav>
            <ul class="nav-main">
                <!-- Ocultar "Inicio" -->
                <li style="display: none;"><a href="index.php"><i class="fa fa-home"></i> Inicio</a></li>
                <!-- Cambiar "Cerrar Sesión" por "Continuar Comprando" -->
                <li><a href="index.php"><i class="fa fa-sign-out-alt"></i> Continuar Comprando</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Carrito de Compras</h2>
        <div class="carrito">
            <?php if (empty($carrito)): ?>
                <p>El carrito está vacío.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($carrito as $id => $cantidad):
                            $sql = "SELECT * FROM productos WHERE id = $id";
                            $result = $conn->query($sql);
                            if ($result && $result->num_rows > 0) {
                                $product = $result->fetch_assoc();
                                $total += $product['precio'] * $cantidad;
                                ?>
                                <tr>
                                    <td><?php echo $product['nombre']; ?></td>
                                    <td><?php echo $cantidad; ?></td>
                                    <td>$<?php echo $product['precio']; ?></td>
                                    <td>$<?php echo $product['precio'] * $cantidad; ?></td>
                                </tr>
                                <?php
                            }
                        endforeach;
                        ?>
                    </tbody>
                </table>
                <p>Total: $<?php echo $total; ?></p>
                <a href="checkout.php">Proceder al Pago</a>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>