<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: login.php");
    exit();
}

include 'config/database.php';

$message = $error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $imagen = basename($_FILES['imagen']['name']);
    $target_file = "img/" . $imagen;
    $stock = $_POST['stock'];
    move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file);

    $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen, usuario_id, stock) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsii", $nombre, $descripcion, $precio, $imagen, $_SESSION['usuario_id'], $stock);

    if ($stmt->execute()) {
        $message = "Producto agregado exitosamente";
    } else {
        $error = "Error al agregar producto: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Producto - Tienda de Computadoras</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        nav {
            position: absolute;
            right: 20px;
        }

        nav a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
            cursor: pointer;
        }

        nav .dropdown {
            display: inline-block;
        }

        nav .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #333;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        nav .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }

        nav .dropdown-content a:hover {
            background-color: #575757;
        }

        nav .dropdown:hover .dropdown-content {
            display: block;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        form {
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        label {
            display: block;
            margin: 10px 0;
            text-align: left;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }

        button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
        }

        button i {
            margin-right: 5px;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <header>
        <h1>PC-XPRESS</h1>
        <nav>
            <div class="dropdown">
                <a><i class="fas fa-bars"></i> Menú</a>
                <div class="dropdown-content">
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
        <div class="container">
            <h2>AGREGAR PRODUCTO</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" required></textarea>
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" id="precio" name="precio" required>
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" required>
                <label for="imagen">Imagen:</label>
                <input type="file" id="imagen" name="imagen" required>
                <div class="center">
                    <button type="submit"><i class="fas fa-save"></i> Agregar Producto</button>
                </div>
            </form>
            <?php if ($message): ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
            <?php if ($error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>