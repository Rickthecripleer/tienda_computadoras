<?php
session_start();
include 'config/database.php';

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    die("Acceso denegado");
}

// Manejar la actualización del rol del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_name = $_POST['new_name']; // Nuevo nombre
    $new_role = $_POST['new_role'];

    $update_sql = "UPDATE usuarios SET nombre = ?, rol_id = ? WHERE id = ?"; // Actualización también del nombre
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $new_name, $new_role, $user_id);

    if ($stmt->execute()) {
        $message = "Usuario actualizado correctamente"; // Mensaje modificado
    } else {
        $message = "Error al actualizar el usuario: " . $conn->error;
    }
}

// Obtener la lista de usuarios
$sql = "SELECT id, nombre, email, rol_id FROM usuarios";
$result = $conn->query($sql);

// Verificar si se recuperaron los usuarios correctamente
if (!$result) {
    die("Error al recuperar los usuarios: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - PC-XPRESS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            display: flex;
            justify-content: center;
            position: relative;
        }

        header h1 {
            font-size: 24px;
            margin-right: 30px;
        }

        .back-to-home {
            position: absolute;
            top: 50%;
            left: 20px;
            transform: translateY(-50%);
        }

        .back-to-home button {
            padding: 5px;
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        .back-to-home button i {
            color: #fff;
            font-size: 20px;
        }

        main {
            width: 80%;
            margin: 0 auto;
        }

        h2 {
            color: #333;
            text-align: center;
        }

        .message {
            color: green;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #333;
            color: #fff;
        }

        td select {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            background-color: #fff;
            cursor: pointer;
        }

        td button {
            padding: 8px 16px;
            background-color: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        td button:hover {
            background-color: #575757;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <header>
        <h1>GESTIÓN DE USUARIOS</h1>
        <div class="back-to-home">
            <button onclick="window.location.href='index.php'"><i class="fas fa-home"></i></button>
        </div>
    </header>
    <main>
        <h2>Lista de Usuarios</h2>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo str_replace("Rol", "Usuario", $message); ?></p>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td>
                            <form method="POST" action="admin_users.php">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <input type="text" name="new_name" value="<?php echo htmlspecialchars($row['nombre']); ?>">
                        </td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td>
                            <select name="new_role">
                                <option value="1" <?php if ($row['rol_id'] == 1)
                                    echo 'selected'; ?>>Admin</option>
                                <option value="2" <?php if ($row['rol_id'] == 2)
                                    echo 'selected'; ?>>Empleado</option>
                                <option value="3" <?php if ($row['rol_id'] == 3)
                                    echo 'selected'; ?>>Cliente</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit">Actualizar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>© 2025 PC-XPRESS</p>
    </footer>
</body>

</html>