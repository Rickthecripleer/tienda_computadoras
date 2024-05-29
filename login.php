<?php
session_start();
include 'config/database.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['usuario_id'] = $user['id'];
            $_SESSION['usuario_nombre'] = $user['nombre']; // Almacena el nombre del usuario en la sesión
            $_SESSION['usuario_rol'] = $user['rol_id']; // Almacena el rol en la sesión
            header("Location: index.php");
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Tienda de Computadoras</title>
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

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 16px;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <form method="post">
            <div class="form-group">
                <label for="email" class="form-label"><i class="fas fa-envelope"></i></label>
                <input type="email" id="email" name="email" class="form-input" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password" class="form-label"><i class="fas fa-lock"></i></label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Contraseña"
                    required>
            </div>
            <button type="submit" class="form-submit"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</button>
        </form>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <div class="register-link">
            <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
        </div>
    </div>
</body>

</html>

<?php $conn->close(); ?>