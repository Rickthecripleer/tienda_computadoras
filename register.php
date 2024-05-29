<?php
include 'config/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol_id = 3; // Cliente

    $sql = "INSERT INTO usuarios (nombre, email, password, rol_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $email, $password, $rol_id);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Error en el registro: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrarse - Tienda de Computadoras</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #4CAF50;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            color: #555;
            margin-right: 10px;
            font-size: 18px;
        }

        .form-input {
            width: calc(100% - 30px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-submit {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .form-submit:hover {
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
    <div class="container">
        <h2>Registrarse</h2>
        <form method="post">
            <div class="form-group">
                <label for="nombre" class="form-label"><i class="fas fa-user"></i></label>
                <input type="text" id="nombre" name="nombre" class="form-input" placeholder="Nombre" required>
            </div>
            <div class="form-group">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label"><i class="fas fa-lock"></i></label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Contraseña"
                    required>
            </div>
            <button type="submit" class="form-submit"><i class="fas fa-user-plus"></i> Registrarse</button>
        </form>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>

<?php $conn->close(); ?>