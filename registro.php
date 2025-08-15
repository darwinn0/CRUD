<?php
// registro.php
session_start();
include("conexion.php");

$mensaje_registro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario'] ?? '');
    $password_ingresada = trim($_POST['password'] ?? '');

    if (empty($usuario) || empty($password_ingresada)) {
        $mensaje_registro = "Por favor, completa todos los campos.";
    } else {
        if ($conn) {
            // Genera un hash seguro de la contraseña
            // PASSWORD_DEFAULT es la opción recomendada, usa el algoritmo más fuerte disponible (actualmente bcrypt).
            $hashed_password = password_hash($password_ingresada, PASSWORD_DEFAULT);

            // Prepara la consulta para insertar el nuevo usuario
            $stmt = $conn->prepare("INSERT INTO t_usuario (usuario, password) VALUES (?, ?)");

            if ($stmt) {
                // 'ss' indica que ambos parámetros son strings
                $stmt->bind_param("ss", $usuario, $hashed_password);

                if ($stmt->execute()) {
                    $mensaje_registro = "¡Registro exitoso! Ahora puedes iniciar sesión.";
                    // Opcional: redirigir directamente al login o al dashboard
                    // header("Location: index.php?registro=exitoso");
                    // exit();
                } else {
                    // Posible error si el usuario ya existe (debido a UNIQUE en la columna usuario)
                    if ($conn->errno == 1062) { // Código de error para duplicado de entrada
                        $mensaje_registro = "El nombre de usuario ya existe. Por favor, elige otro.";
                    } else {
                        $mensaje_registro = "Error al registrar el usuario: " . $stmt->error;
                    }
                }
                $stmt->close();
            } else {
                $mensaje_registro = "Error interno del sistema al preparar la consulta de registro.";
            }
            $conn->close();
        } else {
            $mensaje_registro = "Error al conectar con la base de datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-image: url('Fondo.jpg'); /* Asegúrate de tener esta imagen */
            background-size: cover;
            background-repeat: no-repeat;
        }
        .register-container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 2.5rem;
            height: 52px;
            font-size: 1.1rem;
        }
        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
            width: 100%;
        }
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgb(100, 100, 100);
            z-index: 2;
        }
        .btn-animated {
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            width: 100%;
            display: block;
            font-size: 1.1rem;
        }
        .btn-animated:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
        }
    </style>
</head>
<body>

<div class="register-container">
    <h3>Registro de Usuario</h3>

    <?php if (!empty($mensaje_registro)): ?>
        <div class="alert alert-info text-center"><?= $mensaje_registro ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <span class="input-icon"><i class="bi bi-person-fill"></i></span>
            <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Nombre de usuario" required>
        </div>
        <div class="input-group">
            <span class="input-icon"><i class="bi bi-lock-fill"></i></span>
            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
        </div>
        <button type="submit" class="btn btn-primary fw-bold btn-animated">Registrarse</button>
    </form>

    <p class="mt-3 text-center">¿Ya tienes una cuenta? <a href="index.php">Inicia Sesión aquí</a></p>
</div>

</body>
</html>