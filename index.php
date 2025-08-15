<?php
// index.php
session_start();

// Incluye tu archivo de conexión a la base de datos
// Asegúrate de que este archivo defina $conn y que la conexión sea exitosa
include("conexion.php"); 

$mensaje_error = "";

// Verifica si se ha enviado el formulario por POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Limpia y obtiene el nombre de usuario y la contraseña ingresada
    $usuario = trim($_POST['usuario'] ?? ''); 
    $password_ingresada = trim($_POST['password'] ?? '');

    // Valida que ambos campos no estén vacíos
    if (empty($usuario) || empty($password_ingresada)) {
        $mensaje_error = "Por favor, ingresa tu usuario y contraseña.";
    } else {
        // Verifica si la conexión a la base de datos es exitosa
        if ($conn) {
            // Prepara la consulta SQL para buscar el usuario
            // Usamos prepared statements para prevenir inyecciones SQL
            $stmt = $conn->prepare("SELECT id_usuario, usuario, password FROM t_usuario WHERE usuario = ?");

            // Verifica si la preparación de la consulta fue exitosa
            if ($stmt) {
                // Vincula el parámetro de usuario a la consulta ('s' indica que es un string)
                $stmt->bind_param("s", $usuario);
                // Ejecuta la consulta
                $stmt->execute();
                // Obtiene el resultado de la consulta
                $result = $stmt->get_result();

                // Si se encontró exactamente un usuario con ese nombre
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    // --- INICIO DE DEPURACIÓN (Descomenta para depurar si tienes problemas) ---
                    /*
                    echo "<pre>";
                    echo "DEBUG: Datos recuperados del usuario:\n";
                    print_r($user);
                    echo "DEBUG: Usuario ingresado: '" . htmlspecialchars($usuario) . "'\n";
                    echo "DEBUG: Contraseña ingresada (texto plano): '" . htmlspecialchars($password_ingresada) . "'\n";
                    echo "DEBUG: Hash de contraseña desde DB: '" . htmlspecialchars($user['password']) . "'\n";
                    echo "DEBUG: Longitud del hash en DB: " . strlen($user['password']) . "\n";
                    echo "DEBUG: Resultado de password_verify(): ";
                    var_dump(password_verify($password_ingresada, $user['password']));
                    echo "</pre>";
                    */
                    // --- FIN DE DEPURACIÓN ---

                    // **Punto clave:** Verifica la contraseña ingresada contra el hash almacenado
                    if (password_verify($password_ingresada, $user['password'])) {
                        // Si las credenciales son correctas, inicia la sesión
                        $_SESSION['loggedin'] = TRUE;
                        $_SESSION['id'] = $user['id_usuario'];
                        $_SESSION['usuario'] = $user['usuario'];

                        // *** CAMBIO AQUÍ: Redirige al usuario a registroProduc.php ***
                        header("Location: registroProduc.php"); 
                        exit(); // Es crucial usar exit() después de header()
                    } else {
                        // Si la contraseña no coincide
                        $mensaje_error = "Contraseña incorrecta. Inténtalo de nuevo.";
                    }
                } else {
                    // Si el usuario no fue encontrado
                    $mensaje_error = "Usuario no encontrado.";
                }

                // Cierra la sentencia preparada
                $stmt->close();
            } else {
                // Error si la preparación de la consulta falla
                $mensaje_error = "Error interno del sistema al preparar la consulta.";
            }
            // Cierra la conexión a la base de datos
            $conn->close();
        } else {
            // Error si la conexión a la base de datos falla
            $mensaje_error = "Error al conectar con la base de datos. Por favor, intenta más tarde.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
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

        .login-container {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
            display: flex;
            flex-wrap: wrap;
            max-width: 900px;
            width: 100%;
            overflow: hidden;
        }

        .logo-section {
            color: white;
            flex: 1 1 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .logo-section img {
            max-width: 100%;
            height: auto;
        }

        .form-section {
            flex: 1 1 400px;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .form-section h3 {
            font-weight: 600;
            color: #2a2a2a;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 2.5rem;
            height: 52px;
            font-size: 1.1rem;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
            width: 100%;
            max-width: 380px;
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
            max-width: 380px;
            width: 100%;
            display: block;
            font-size: 1.1rem;
        }

        .btn-animated:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 123, 255, 0.4);
        }

        .btn-animated:active {
            transform: scale(0.98);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.2);
        }

        .alert-danger {
            max-width: 380px;
            width: 100%;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .form-section {
                padding: 50px 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="logo-section">
        <img src="logo.png" alt="Logo del sistema"> 
    </div>

    <div class="form-section">
        <h3>Iniciar Sesión</h3>

        <?php
        if (!empty($mensaje_error)): ?>
            <div class="alert alert-danger text-center"><?= $mensaje_error ?></div>
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
            <button type="submit" class="btn btn-primary fw-bold btn-animated">Entrar</button>
        </form>

    </div>
</div>

</body>
</html>