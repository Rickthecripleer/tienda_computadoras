<?php
session_start();
include 'config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "INSERT INTO pedidos (usuario_id) VALUES ($usuario_id)";
    if ($conn->query($sql) === TRUE) {
        $pedido_id = $conn->insert_id;
        foreach ($carrito as $producto_id => $cantidad) {
            $sql = "INSERT INTO pedidos_productos (pedido_id, producto_id, cantidad) VALUES ($pedido_id, $producto_id, $cantidad)";
            $conn->query($sql);
        }
        unset($_SESSION['carrito']);
        header("Location: gracias.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Checkout - PC-XPRESS</title>
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
            justify-content: space-between;
            /* Alinear los elementos en el encabezado */
            align-items: center;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            position: relative;
            /* Para posicionar el nav a la izquierda */
        }

        header h1 {
            margin: 0 auto;
            /* Centrar el título */
        }

        nav a {
            color: #fff;
            text-decoration: none;
            margin-right: 20px;
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

        .checkout {
            margin-top: 20px;
            /* Aumenta el espacio superior */
        }

        .checkout form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .checkout p {
            margin-bottom: 20px;
        }

        .checkout button[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .checkout button[type="submit"]:hover {
            background-color: #45a049;
        }

        .resumen-pedido {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .resumen-pedido table {
            width: 100%;
            border-collapse: collapse;
        }

        .resumen-pedido th,
        .resumen-pedido td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .resumen-pedido th {
            background-color: #f2f2f2;
        }

        .resumen-pedido td {
            background-color: #fff;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
        </nav>
        <h1>PC-XPRESS</h1>
        <nav>
            <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Regresar al Carrito</a>
        </nav>
    </header>
    <main>
        <h2>Checkout</h2>
        <div class="checkout">
            <div class="resumen-pedido">
                <h3>Resumen del Pedido</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($carrito as $producto_id => $cantidad) {
                            $sql = "SELECT nombre, precio FROM productos WHERE id = $producto_id";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $nombre = $row['nombre'];
                                $precio = $row['precio'];
                                $subtotal = $precio * $cantidad;
                                $total += $subtotal;
                                echo "<tr>
                                        <td>{$nombre}</td>
                                        <td>{$cantidad}</td>
                                        <td>$" . number_format($precio, 2) . "</td>
                                        <td>$" . number_format($subtotal, 2) . "</td>
                                    </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <p><strong>Total: $<?php echo number_format($total, 2); ?></strong></p>
            </div>
            <form method="post">
                <p>Confirme su compra:</p>
                <button type="submit">Confirmar</button>
            </form>
        </div>
    </main>
</body>

</html>

<?php $conn->close(); ?>