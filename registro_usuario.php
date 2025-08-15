<?php
include("conexion.php");

$mensaje = "";
$errores = [];

// --- Obtener roles y departamentos ---
$roles = [];
$departamentos = [];

$sql_roles = "SELECT id_rol, nombre_rol FROM t_rol ORDER BY nombre_rol ASC";
$resultado_roles = $conn->query($sql_roles);
if ($resultado_roles) {
    while ($fila = $resultado_roles->fetch_assoc()) {
        $roles[] = $fila;
    }
} else {
    die("Error: La tabla 't_rol' no existe.");
}

$sql_departamentos = "SELECT id_departamento, nombre_departamento FROM t_departamento ORDER BY nombre_departamento ASC";
$resultado_departamentos = $conn->query($sql_departamentos);
if ($resultado_departamentos) {
    while ($fila = $resultado_departamentos->fetch_assoc()) {
        $departamentos[] = $fila;
    }
} else {
    die("Error: La tabla 't_departamento' no existe.");
}

// --- Procesar formulario ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $dni = trim($_POST['dni'] ?? '');
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $rol = intval($_POST['rol'] ?? 0);
    $departamento = intval($_POST['departamento'] ?? 0);
    $fecha_registro = date("Y-m-d");

    if (empty($usuario) || empty($nombre) || empty($dni) || empty($rol) || empty($departamento)) {
        $errores[] = "Los campos obligatorios no pueden estar vacíos.";
    }

    if (empty($errores)) {
        if ($id_usuario > 0) {
            $sql = "UPDATE t_usuario SET nombre=?, usuario=?, telefono=?, correo=?, dni=?, genero=?, fecha_nacimiento=?, id_rol=?, id_departamento=?";
            $params = [$nombre, $usuario, $telefono, $correo, $dni, $genero, $fecha_nacimiento, $rol, $departamento];
            $types = "sssssssii";

            if (!empty($password)) {
                if (strlen($password) < 6) {
                    $errores[] = "La contraseña debe tener al menos 6 caracteres.";
                } else {
                    $sql .= ", password=?";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                    $types .= "s";
                }
            }

            if (empty($errores)) {
                $sql .= " WHERE id_usuario=?";
                $params[] = $id_usuario;
                $types .= "i";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute() ? $mensaje = "✅ Usuario actualizado correctamente." : $errores[] = "❌ Error: " . $stmt->error;
                $stmt->close();
            }
        } else {
            if (strlen($password) < 6) {
                $errores[] = "La contraseña debe tener al menos 6 caracteres.";
            } else {
                $stmt_check = $conn->prepare("SELECT id_usuario FROM t_usuario WHERE usuario=? OR dni=?");
                $stmt_check->bind_param("ss", $usuario, $dni);
                $stmt_check->execute();
                $stmt_check->store_result();
                if ($stmt_check->num_rows > 0) {
                    $errores[] = "Usuario o DNI ya existen.";
                }
                $stmt_check->close();

                if (empty($errores)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt_insert = $conn->prepare("INSERT INTO t_usuario (nombre, usuario, password, telefono, correo, dni, genero, fecha_nacimiento, id_rol, id_departamento, fecha_registro) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt_insert->bind_param("ssssssssiis", $nombre, $usuario, $hash, $telefono, $correo, $dni, $genero, $fecha_nacimiento, $rol, $departamento, $fecha_registro);
                    $stmt_insert->execute() ? $mensaje = "✅ Usuario registrado." : $errores[] = "❌ Error: " . $stmt_insert->error;
                    $stmt_insert->close();
                }
            }
        }
    }
}

// --- Consulta de usuarios ---
$sql_usuarios = "SELECT u.id_usuario, u.nombre, u.usuario, u.correo, u.telefono, u.dni, u.genero, u.fecha_nacimiento, u.id_rol, u.id_departamento, r.nombre_rol, d.nombre_departamento 
FROM t_usuario u 
LEFT JOIN t_rol r ON u.id_rol = r.id_rol 
LEFT JOIN t_departamento d ON u.id_departamento = d.id_departamento 
ORDER BY u.id_usuario DESC";

$resultado_usuarios = $conn->query($sql_usuarios);
if (!$resultado_usuarios) {
    die("❌ Error al ejecutar la consulta de usuarios: " . $conn->error);
}
?>
<!-- HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body{background-image:url(Fondo.jpg);background-size:cover;background-attachment:fixed;padding-top:40px;display:flex;flex-direction:column;align-items:center;min-height:100vh;}
    .card,.table-container{background-color:rgba(255,255,255,.95);color:#212529;border-radius:12px;width:95%;max-width:1200px;padding:30px;margin-bottom:30px;}
    .form-control:focus{border-color:#198754;box-shadow:0 0 0 .2rem rgba(25,135,84,.25);}
    .btn-animated{transition:transform .2s ease;}.btn-animated:hover{transform:translateY(-2px);}
    table{min-width:1000px;font-size:.95rem;}th,td{text-align:center;white-space:nowrap;}
  </style>
</head>
<body>
<div class="card p-4">
  <h3 id="form-title" class="text-center mb-4">Registrar Nuevo Usuario</h3>
  <?php if (!empty($mensaje)): ?><div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
  <?php if (!empty($errores)): ?><div class="alert alert-danger"><ul><?php foreach ($errores as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="POST" id="user-form">
    <input type="hidden" name="id_usuario" id="id_usuario" value="0">
    <div class="row g-3">
      <div class="col-md-6"><label>Nombre completo</label><input type="text" name="nombre" class="form-control" required></div>
      <div class="col-md-6"><label>Usuario</label><input type="text" name="usuario" class="form-control" required></div>
      <div class="col-md-6"><label>DNI</label><input type="text" name="dni" class="form-control" required></div>
      <div class="col-md-6"><label>Contraseña</label><input type="password" name="password" class="form-control" placeholder="Dejar en blanco si no cambia"></div>
      <div class="col-md-6"><label>Teléfono</label><input type="text" name="telefono" class="form-control"></div>
      <div class="col-md-6"><label>Correo</label><input type="email" name="correo" class="form-control"></div>
      <div class="col-md-6"><label>Fecha nacimiento</label><input type="date" name="fecha_nacimiento" class="form-control"></div>
      <div class="col-md-6"><label>Género</label><select name="genero" class="form-select"><option value="">Seleccione...</option><option value="Masculino">Masculino</option><option value="Femenino">Femenino</option></select></div>
      <div class="col-md-6"><label>Rol</label><select name="rol" class="form-select" required><option value="">Seleccione...</option><?php foreach ($roles as $r): ?>
        <option value="<?= htmlspecialchars($r['id_rol']) ?>"><?= htmlspecialchars($r['nombre_rol']) ?></option><?php endforeach; ?></select>
      </div>
      <div class="col-md-6"><label>Departamento</label><select name="departamento" class="form-select" required><option value="">Seleccione...</option><?php foreach ($departamentos as $d): ?><option value="<?= htmlspecialchars($d['id_departamento']) ?>"><?= htmlspecialchars($d['nombre_departamento']) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="d-grid gap-2 mt-4">
      <button type="submit" class="btn btn-success btn-animated fw-bold">Guardar Cambios</button>
      <button type="button" onclick="resetForm()" class="btn btn-secondary btn-animated">Cancelar Edición</button>
      <a href="registroProduc.php" class="btn btn-outline-primary btn-animated text-center">Volver al registro</a>
    </div>
  </form>
</div>

<div class="table-container p-4">
  <h4 class="text-center">Usuarios Registrados</h4>
  <input type="text" id="buscar" class="form-control mb-2" placeholder="Buscar por nombre, usuario, DNI o correo...">
  <table class="table table-bordered table-hover" id="tablaUsuarios">
    <thead class="table-success"><tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>DNI</th><th>Correo</th><th>Teléfono</th><th>Rol</th><th>Departamento</th><th>Acciones</th></tr></thead>
    <?php if ($resultado_usuarios && $resultado_usuarios->num_rows > 0): ?>
    <tbody>
      <?php while ($fila = $resultado_usuarios->fetch_assoc()): ?>
        <tr ondblclick='cargarUsuario(<?= json_encode($fila) ?>)' style="cursor:pointer;">
          <td><?= htmlspecialchars($fila['id_usuario']) ?></td>
          <td><?= htmlspecialchars($fila['nombre']) ?></td>
          <td><?= htmlspecialchars($fila['usuario']) ?></td>
          <td><?= htmlspecialchars($fila['dni']) ?></td>
          <td><?= htmlspecialchars($fila['correo']) ?></td>
          <td><?= isset($fila['telefono']) ? htmlspecialchars($fila['telefono']) : 'No definido' ?></td>
          <td><?= isset($fila['nombre_rol']) ? htmlspecialchars($fila['nombre_rol']) : 'N/A' ?></td>
          <td><?= isset($fila['nombre_departamento']) ? htmlspecialchars($fila['nombre_departamento']) : 'N/A' ?></td>
          <td>
            <button class="btn btn-sm btn-warning btn-animated" onclick='cargarUsuario(<?= json_encode($fila) ?>)'>Editar</button>
            <button class="btn btn-sm btn-danger btn-animated" onclick="event.stopPropagation(); eliminarUsuario(this, <?= $fila['id_usuario'] ?>)">Eliminar</button>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
    <?php else: ?>
    <tbody><tr><td colspan="9" class="text-center">No hay usuarios para mostrar</td></tr></tbody>
    <?php endif; ?>
  </table>
</div>

<script>
const form = document.getElementById("user-form"),
      formTitle = document.getElementById("form-title"),
      originalTitle = formTitle.textContent,
      hiddenIdInput = document.getElementById("id_usuario");

function cargarUsuario(u) {
  hiddenIdInput.value = u.id_usuario;
  form.nombre.value = u.nombre || "";
  form.usuario.value = u.usuario || "";
  form.dni.value = u.dni || "";
  form.correo.value = u.correo || "";
  form.telefono.value = u.telefono || "";
  form.fecha_nacimiento.value = u.fecha_nacimiento || "";
  form.genero.value = u.genero || "";
  form.rol.value = u.id_rol || "";
  form.departamento.value = u.id_departamento || "";
  formTitle.textContent = `Editando Usuario: ${u.nombre}`;
  formTitle.style.color = "#ffc107";
  form.querySelector('button[type="submit"]').textContent = "Actualizar Usuario";
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function resetForm() {
  form.reset();
  hiddenIdInput.value = "0";
  formTitle.textContent = originalTitle;
  formTitle.style.color = "";
  form.querySelector('button[type="submit"]').textContent = "Guardar Cambios";
}

document.getElementById("buscar").addEventListener("input", function() {
  const filtro = this.value.toLowerCase();
  document.querySelectorAll("#tablaUsuarios tbody tr").forEach(row => {
    row.style.display = row.innerText.toLowerCase().includes(filtro) ? "" : "none";
  });
});

function eliminarUsuario(btn, id) {
  if (!confirm(`¿Seguro que deseas eliminar el usuario con ID ${id}? Esta acción no se puede deshacer.`)) return;
  const datos = new FormData();
  datos.append("id", id);
  fetch("eliminar_usuario.php", { method: "POST", body: datos })
    .then(r => r.text())
    .then(r => {
      if (r.trim() === "ok") {
        btn.closest("tr").remove();
        alert("✅ Usuario eliminado correctamente.");
        resetForm();
      } else {
        alert(`❌ Error al eliminar el usuario: ${r}`);
      }
    })
    .catch(err => alert(`❌ Error de conexión: ${err}`));
}
</script>
</body>
</html>
