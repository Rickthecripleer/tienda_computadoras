<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: login.php");
    exit();
}

include 'config/database.php';

$message = $error = '';

$id = $_GET['id'];
$sql = "SELECT * FROM productos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete'])) {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Error al eliminar producto: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock']; // Captura del campo stock

        // Manejo de la imagen, utilizando la imagen existente si no se carga una nueva
        $imagen = $product['imagen']; // Utiliza la imagen existente por defecto
        if ($_FILES['imagen']['name']) {
            $imagen = basename($_FILES['imagen']['name']);
            $target_file = "img/" . $imagen;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file);
        }

        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, imagen = ?, stock = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsii", $nombre, $descripcion, $precio, $imagen, $stock, $id);

        if ($stmt->execute()) {
            $message = "Producto actualizado exitosamente";
            // Actualiza los detalles del producto después de la actualización
            $sql = "SELECT * FROM productos WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
        } else {
            $error = "Error al actualizar producto: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Tienda de Computadoras</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Estilos adicionales */
        body {
            font-family: 'Oswald', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        header {
            background-color: #333333;
            color: #fff;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        nav {
            display: flex;
            gap: 15px;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
            display: flex;
            align-items: center;
        }

        nav a i {
            margin-right: 5px;
        }

        nav a:hover {
            background-color: #45a049;
        }

        header h1 {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            font-size: 24px;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #FF0000;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        textarea {
            height: 150px;
        }

        img {
            display: block;
            max-width: 200px;
            margin: 0 auto 10px;
        }

        .center {
            text-align: center;
            margin-top: 20px;
        }

        .center button {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .center button:hover {
            background-color: #45a049;
        }

        .error {
            color: #ff0000;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="carrito.php"><i class="fas fa-shopping-cart"></i> Carrito</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_rol'] == 1): ?>
                <a href="add_product.php"><i class="fas fa-plus"></i> Agregar Producto</a>
            <?php endif; ?>
        </nav>
        <h1>PC-XPRESS</h1>
    </header>
    <main>
        <div class="container">
            <h2>EDITAR PRODUCTO</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="nombre">NOMBRE:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($product['nombre']); ?>"
                    required>
                <label for="descripcion">DESCRIPCIÓN:</label>
                <textarea id="descripcion" name="descripcion"
                    required><?php echo htmlspecialchars($product['descripcion']); ?></textarea>
                <label for="precio">PRECIO:</label>
                <input type="number" step="0.01" id="precio" name="precio" value="<?php echo $product['precio']; ?>"
                    required>
                <label for="stock">STOCK:</label>
                <input type="number" id="stock" name="stock" value="<?php echo $product['stock']; ?>" required>
                <label for="imagen">IMAGEN ACTUAL:</label><br>
                <img src="img/<?php echo htmlspecialchars($product['imagen']); ?>" alt="Imagen del producto">
                <label for="imagen">SI NO DESEAS CAMBIAR LA IMAGEN, IGNORA ESTÁ OPCIÓN.</label>
                <input type="file" id="imagen" name="imagen">
                <div class="center">
                    <button type="submit"><i class="fas fa-save"></i> Actualizar Producto</button>
                    <button type="submit" name="delete" style="background-color: #f44336;"><i class="fas fa-trash"></i>
                        Eliminar Producto</button>
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