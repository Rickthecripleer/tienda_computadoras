<?php
include 'config/database.php';

// Verifica si se proporcionó un ID de producto en la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta la base de datos para obtener los detalles del producto con el ID proporcionado
    $sql = "SELECT id, nombre, descripcion, precio, imagen, usuario_id, stock FROM productos WHERE id = $id";
    $result = $conn->query($sql);

    // Verifica si se encontró un producto con el ID proporcionado
    if ($result->num_rows > 0) {
        // Obtiene los detalles del producto
        $product = $result->fetch_assoc();
    } else {
        // Si no se encontró ningún producto, puedes mostrar un mensaje de error o redirigir a una página de error
        echo "Producto no encontrado";
        exit;
    }
} else {
    // Si no se proporcionó ningún ID de producto, puedes mostrar un mensaje de error o redirigir a una página de error
    echo "ID de producto no proporcionado";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?php echo $product['nombre']; ?> - Tienda de Computadoras</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Estilos adicionales */
        body {
            font-family: 'Oswald', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            /* Alinea los elementos a los extremos */
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
            /* Tamaño del título */
            text-align: center;
            /* Centrar el texto */
            flex-grow: 1;
            /* Expande el título para ocupar el espacio restante */
        }

        .nav-dropdown {
            position: relative;
            color: #fff;
            /* Color blanco */
        }

        .nav-dropdown-content {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #fff;
            /* Cambio a blanco */
            border-radius: 5px;
            overflow: hidden;
            display: none;
            z-index: 1;
            /* Asegura que el menú esté por encima de otros elementos */
            box-shadow: 0 4px 8px rgba(255, 0, 0, 0.2);
            /* Sombra roja más suave */
        }

        .nav-dropdown:hover .nav-dropdown-content {
            display: block;
        }

        .nav-dropdown-content a {
            color: #333;
            /* Cambio a color oscuro */
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            transition: background-color 0.3s;
            box-shadow: 0 2px 4px rgba(255, 0, 0, 0.1);
            /* Sombra roja más suave */
        }

        .nav-dropdown-content a:hover {
            background-color: #f5f5f5;
            /* Cambio a un tono más claro */
        }

        .nav-dropdown-content i {
            margin-right: 10px;
        }

        main {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1,
        h2 {
            margin-top: 20px;
            /* Aumenta el espacio superior */
            margin-bottom: 10px;
            /* Aumenta el espacio inferior */
        }

        .producto-detalle {
            margin-top: 20px;
        }

        .producto-detalle img {
            max-width: 100%;
            height: auto;
            max-height: 300px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .producto-detalle p {
            margin-bottom: 10px;
        }

        .producto-detalle form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .producto-detalle label {
            margin-bottom: 5px;
        }

        .producto-detalle input[type="number"] {
            width: 60px;
            padding: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .producto-detalle button[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .producto-detalle button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <header>
        <h1>PC-XPRESS</h1>
        <nav>
            <div class="nav-dropdown">
                <i class="fas fa-bars" style="color: white;"></i>
                <div class="nav-dropdown-content">
                    <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
                    <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] == 1): ?>
                        <a href="add_product.php"><i class="fas fa-plus"></i> Agregar Producto</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <h1><?php echo $product['nombre']; ?></h1>
        <div class="producto-detalle">
            <img src="img/<?php echo $product['imagen']; ?>" alt="<?php echo $product['nombre']; ?>">
            <p><?php echo $product['descripcion']; ?></p>
            <p>$<?php echo $product['precio']; ?></p>
            <form action="carrito.php" method="post">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <p>Stock disponible: <?php echo $product['stock']; ?></p>
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" value="1" min="1"
                    max="<?php echo $product['stock']; ?>">
                <button type="submit"><i class="fas fa-cart-plus"></i> Agregar al Carrito</button>
            </form>
        </div>
    </main>
</body>

</html>