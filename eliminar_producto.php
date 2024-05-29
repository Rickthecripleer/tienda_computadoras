<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] != 1) {
    header("Location: login.php");
    exit();
}

include 'config/database.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Producto eliminado exitosamente";
        header("Location: index.php");
        exit();
    } else {
        $error = "Error al eliminar producto: " . $stmt->error;
    }

    $stmt->close();
} else {
    $error = "ID de producto no válido";
}

$conn->close();
?>