<?php
session_start();
include 'config/database.php';

// Recuperar el término de búsqueda si existe
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM productos";
if ($search) {
    $sql .= " WHERE nombre LIKE '%$search%' OR descripcion LIKE '%$search%'";
}

$result = $conn->query($sql);

// Verificar si se recuperaron los productos correctamente
if (!$result) {
    die("Error al recuperar los productos: " . $conn->error);
}

// Obtener el primer nombre del usuario y su rol si están en la sesión
$primer_nombre = '';
$rol_usuario = '';
if (isset($_SESSION['usuario_nombre'])) {
    $nombre_completo = $_SESSION['usuario_nombre'];
    $partes_nombre = explode(' ', $nombre_completo);
    $primer_nombre = $partes_nombre[0];
    $rol_usuario = isset($_SESSION['usuario_rol']) ? $_SESSION['usuario_rol'] : '';
    // Convertir el rol a una cadena descriptiva
    switch ($rol_usuario) {
        case 1:
            $rol_usuario = 'Administrador';
            break;
        case 2:
            $rol_usuario = 'Empleado';
            break;
        case 3:
            $rol_usuario = ''; // Cliente no muestra el rol
            break;
        default:
            $rol_usuario = '';
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PC-XPRESS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Estilo para el encabezado */
        header {
            background-color: #333333;
            padding: 15px 20px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .user-info {
            position: absolute;
            left: 20px;
            font-size: 1.2em;
            display: flex;
            align-items: center;
        }

        .user-info i {
            margin-right: 8px;
        }

        .user-info span {
            margin-left: 5px;
            font-size: 0.9em;
            color: #cccccc;
        }

        /* Estilo para el título */
        header h1 {
            margin: 0 auto;
            font-size: 3.5em;
            color: white;
            text-shadow:
                -1px -1px 0 red,
                1px -1px 0 red,
                -1px 1px 0 red,
                1px 1px 0 red;
            animation: tituloAnimacion 1s ease-in-out infinite alternate;
        }

        @keyframes tituloAnimacion {
            from {
                transform: scale(1);
            }

            to {
                transform: scale(1.1);
            }
        }

        /* Estilo para el menú desplegable */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #FF9999
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Estilo para las imágenes de los productos */
        .producto {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 10px;
            border-radius: 10px;
            transition: transform 0.3s;
        }

        .producto:hover {
            transform: scale(1.05);
        }

        .producto img {
            width: 100%;
            height: auto;
            object-fit: cover;
            display: block;
            margin: auto;
        }

        /* Estilo para los botones */
        .button {
            font-size: 14px;
            padding: 10px 15px;
            width: auto;
            display: inline-block;
            background-color: #FFCCCC;
            /* Fondo más rojizo */
            color: #333;
            /* Color del texto */
            border: 1px solid #ccc;
            /* Borde gris claro */
            border-radius: 4px;
            /* Bordes redondeados */
            text-align: center;
            /* Centrar el texto */
            cursor: pointer;
            /* Cambia el cursor al pasar por encima */
            transition: background-color 0.3s ease;
            /* Suaviza el cambio de color de fondo */
        }

        .button:hover {
            background-color: #FF6666;
            /* Cambia el fondo a un rojo más oscuro al pasar el ratón */
        }

        /* Estilo para limitar la longitud de la descripción */
        .producto p {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 300px;
            /* Ajusta el valor según sea necesario */
        }

        /* Estilo para el pie de página */
        footer {
            background-color: #333333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
        }

        /* Estilo para el formulario de búsqueda */
        .search-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .search-container input[type="text"] {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .search-container button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #333333;
            color: white;
            border: none;
            border-radius: 4px;
            margin-left: 10px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #FF6666;
        }
    </style>
</head>

<body>
    <header>
        <h1>PC-XPRESS</h1>
        <?php if ($primer_nombre): ?>
            <div class="user-info">
                <i class="fa fa-user"></i>
                Bienvenido, <?php echo htmlspecialchars($primer_nombre); ?>
                <?php if ($rol_usuario && $rol_usuario != 'Usuario'): ?>
                    <span>(<?php echo htmlspecialchars($rol_usuario); ?>)</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="dropdown">
            <i class="fa fa-bars"></i>
            <div class="dropdown-content">
                <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <a href="login.php"><i class="fa fa-sign-in-alt"></i> Iniciar Sesión</a>
                <?php else: ?>
                    <?php if ($_SESSION['usuario_rol'] == 1): ?>
                        <a href="add_product.php"><i class="fa fa-plus"></i> Agregar Producto</a>
                        <a href="admin_users.php"><i class="fa fa-users"></i> Gestión de Usuarios</a>
                        <a href="administrar_pedidos_productos.php"><i class="fa fa-tasks"></i> Admin. Pedidos Productos</a>
                    <?php elseif ($_SESSION['usuario_rol'] == 2): ?>
                        <a href="administrar_pedidos_productos.php"><i class="fa fa-tasks"></i> Admin. Pedidos Productos</a>
                    <?php endif; ?>
                    <a href="carrito.php"><i class="fa fa-shopping-cart"></i> Carrito</a>
                    <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Cerrar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>
        <h2>PRODUCTOS</h2>

        <!-- Formulario de búsqueda -->
        <div class="search-container">
            <form action="index.php" method="get">
                <input type="text" name="search" placeholder="Buscar productos..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fa fa-search"></i> Buscar</button>
            </form>
        </div>

        <div class="productos">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="producto">
                    <?php
                    // Ruta de la imagen
                    $imagen = htmlspecialchars($row['imagen']);
                    $ruta_imagen = "img/$imagen";
                    ?>
                    <img src="<?php echo $ruta_imagen; ?>" alt="Imagen de <?php echo htmlspecialchars($row['nombre']); ?>">
                    <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                    <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                    <p>$<?php echo htmlspecialchars($row['precio']); ?></p>
                    <a href="producto.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="button">COMPRAR</a>
                    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] == 1): ?>
                        <a href="edit_product.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="button">EDITAR</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
    <footer>
        <p>© 2025 PC-XPRESS</p>
    </footer>
</body>
</html>