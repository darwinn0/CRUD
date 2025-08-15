
<?php
include("conexion.php");

$mensaje = "";
$errores = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    if (empty($usuario)) {
        $errores[] = "El nombre de usuario es obligatorio.";
    }

    if (strlen($password) < 6) {
        $errores[] = "La contraseña debe tener al menos 6 caracteres.";
    }

    if ($password !== $confirmar) {
        $errores[] = "Las contraseñas no coinciden.";
    }

    if (empty($errores)) {
        $sql_check = "SELECT id_usuario FROM t_usuario WHERE usuario = ?";
        $stmt_check = $conexion->prepare($sql_check);
        if (!$stmt_check) {
            die("❌ Error al preparar la consulta: " . $conexion->error);
        }
        $stmt_check->bind_param("s", $usuario);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $errores[] = "El nombre de usuario ya está registrado.";
        }

        $stmt_check->close();
    }

    if (empty($errores)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql_insert = "INSERT INTO t_usuario (usuario, password) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        if (!$stmt_insert) {
            die("❌ Error al preparar la consulta: " . $conexion->error);
        }
        $stmt_insert->bind_param("ss", $usuario, $password_hash);

        if ($stmt_insert->execute()) {
            $mensaje = "✅ Usuario registrado correctamente.";
        } else {
            $errores[] = "❌ Error al registrar usuario: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    }

    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Nuevo Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">

<div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
  <h3 class="text-center mb-4">Registrar Nuevo Usuario</h3>

  <?php if (!empty($mensaje)): ?>
    <div class="alert alert-success text-center"><?= $mensaje ?></div>
  <?php endif; ?>

  <?php if (!empty($errores)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errores as $error): ?>
          <li><?= $error ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="">
    <div class="mb-3">
      <label for="usuario" class="form-label">Usuario</label>
      <input type="text" class="form-control" id="usuario" name="usuario" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Contraseña</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
      <label for="confirmar" class="form-label">Confirmar Contraseña</label>
      <input type="password" class="form-control" id="confirmar" name="confirmar" required>
    </div>
    <button type="submit" class="btn btn-primary w-100">Registrar Usuario</button>
    <div class="text-end mt-3">
      <a href="index.php" class="btn btn-success w-100 mt-2 fw-bold shadow-sm" style="transition: transform 0.3s ease, box-shadow 0.3s ease;" data-bs-toggle="tooltip" data-bs-placement="top" title="Volver al inicio de sesión">
        Iniciar sesión
      </a>
    </div>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>

</body>
</html>

