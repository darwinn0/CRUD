
<?php
$mensaje = "";
$conn = new mysqli("localhost", "root", "", "proyectosolo");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// 1. ELIMINAR producto por ID
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['eliminar_id'])) {
    $idEliminar = (int) $_POST['eliminar_id'];
    if ($conn->query("DELETE FROM t_productos WHERE id_producto = $idEliminar")) {
        $mensaje = " Producto eliminado exitosamente.";
    } else {
        $mensaje = " Error al eliminar: " . $conn->error;
    }
}

// 2. EDITAR producto existente
elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['modo_edicion']) && $_POST['modo_edicion'] == "1") {
    $id = (int)$_POST['id_producto'];
    $codigo = $conn->real_escape_string($_POST['codigo']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $modelo = $conn->real_escape_string($_POST['modelo']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $inventario = (int)$_POST['inventario_minimo'];
    $precio = (float)$_POST['precio_compra'];
    $almacenamiento = $conn->real_escape_string($_POST['tipo_almacenamiento']);
    $tiempo_garantia = $conn->real_escape_string($_POST['tiempo_garantia']);

    $res1 = $conn->query("SELECT id_marca FROM t_especificaciones WHERE marca = '$marca' AND modelo = '$modelo' LIMIT 1");
    $id_marca = ($res1 && $res1->num_rows > 0)
        ? $res1->fetch_assoc()['id_marca']
        : ($conn->query("INSERT INTO t_especificaciones (marca, modelo) VALUES ('$marca', '$modelo')") ? $conn->insert_id : 0);

    $res2 = $conn->query("SELECT Id_Garantia FROM t_garantia WHERE Tiempo = '$tiempo_garantia' LIMIT 1");
    $id_garantia = ($res2 && $res2->num_rows > 0)
        ? $res2->fetch_assoc()['Id_Garantia']
        : ($conn->query("INSERT INTO t_garantia (Tiempo) VALUES ('$tiempo_garantia')") ? $conn->insert_id : 0);

    $update = "UPDATE t_productos SET
        codigo = '$codigo',
        nombre = '$nombre',
        categoria = '$categoria',
        estado = '$estado',
        fecha = '$fecha',
        inventario_minimo = $inventario,
        precio_compra = $precio,
        tipo_almacenamiento = '$almacenamiento',
        id_marca = $id_marca,
        Id_Garantia = $id_garantia
        WHERE id_producto = $id";

    $mensaje = $conn->query($update)
        ? "Producto actualizado correctamente."
        : "Error al actualizar: " . $conn->error;
}

// 3. REGISTRO nuevo producto
elseif ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['codigo'], $_POST['nombre'], $_POST['marca'], $_POST['modelo'], $_POST['categoria'], $_POST['estado'], $_POST['fecha'], $_POST['inventario_minimo'], $_POST['precio_compra'], $_POST['tipo_almacenamiento'], $_POST['tiempo_garantia'])) {

    $codigo = $conn->real_escape_string($_POST['codigo']);
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $modelo = $conn->real_escape_string($_POST['modelo']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $fecha = $conn->real_escape_string($_POST['fecha']);
    $inventario = (int)$_POST['inventario_minimo'];
    $precio = (float)$_POST['precio_compra'];
    $almacenamiento = $conn->real_escape_string($_POST['tipo_almacenamiento']);
    $tiempo_garantia = $conn->real_escape_string($_POST['tiempo_garantia']);

    $res1 = $conn->query("SELECT id_marca FROM t_especificaciones WHERE marca = '$marca' AND modelo = '$modelo' LIMIT 1");
    $id_marca = ($res1 && $res1->num_rows > 0)
        ? $res1->fetch_assoc()['id_marca']
        : ($conn->query("INSERT INTO t_especificaciones (marca, modelo) VALUES ('$marca', '$modelo')") ? $conn->insert_id : 0);

    $res2 = $conn->query("SELECT Id_Garantia FROM t_garantia WHERE Tiempo = '$tiempo_garantia' LIMIT 1");
    $id_garantia = ($res2 && $res2->num_rows > 0)
        ? $res2->fetch_assoc()['Id_Garantia']
        : ($conn->query("INSERT INTO t_garantia (Tiempo) VALUES ('$tiempo_garantia')") ? $conn->insert_id : 0);

    $insert = "INSERT INTO t_productos (
        codigo, nombre, categoria, estado, fecha, inventario_minimo,
        precio_compra, tipo_almacenamiento, id_marca, Id_Garantia
    ) VALUES (
        '$codigo', '$nombre', '$categoria', '$estado', '$fecha',
        $inventario, $precio, '$almacenamiento', $id_marca, $id_garantia
    )";

    $mensaje = $conn->query($insert)
        ? "Producto registrado con éxito."
        : " Error al guardar: " . $conn->error;
}

// Búsqueda por código mediante GET
if (isset($_GET['buscar_codigo']) && !empty($_GET['buscar_codigo'])) {
    $codigo_buscado = $conn->real_escape_string($_GET['buscar_codigo']);
    $buscar_sql = "SELECT p.*, e.marca, e.modelo, g.Tiempo AS garantia
                   FROM t_productos p
                   LEFT JOIN t_especificaciones e ON p.id_marca = e.id_marca
                   LEFT JOIN t_garantia g ON p.Id_Garantia = g.Id_Garantia
                   WHERE p.codigo LIKE '%$codigo_buscado%'";
    $buscar_resultado = $conn->query($buscar_sql);
}
?>




<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Formulario Completo</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
</head>

<body style="background-image: url('Fondo.jpg'); background-size: cover; background-repeat: no-repeat;">

<?php
$garantias = [];
$conn = new mysqli("localhost", "root", "", "proyectosolo");
if (!$conn->connect_error) {
  $res = $conn->query("SELECT Id_Garantia, Tiempo FROM t_garantia");
  while ($row = $res->fetch_assoc()) {
    $garantias[] = $row;
  }
  $conn->close();
}
?>
<div class="container mt-5">
  <header class="text-center mb-4">
    <h1 style="color: white;">EMPRESA TECNOLÓGICA</h1>
    <img src="logo.png" class="img-fluid mb-2" width="200px" alt="Logo GP-Lancon">
    <p style="color: white;">DONDE EL MUNDO ES TECNOLOGÍA</p>
  </header>

  <main>
    <div class="card p-4">
      <h2 class="text-center">Formulario de Registro</h2>

      <?php if (!empty($mensaje)) : ?>
        <div class="alert alert-<?php echo str_contains($mensaje, '✅') ? 'success' : 'danger'; ?> text-center">
          <?php echo $mensaje; ?>
        </div>
      <?php endif; ?>

      <form method="POST" autocomplete="off">
        <input type="hidden" id="modo_edicion" name="modo_edicion" value="0">
        <input type="hidden" id="id_producto" name="id_producto" value="">

        <div class="mb-3">
          <label for="codigo" class="form-label">Código</label>
          <input type="text" class="form-control" id="codigo" name="codigo" required autofocus>
        </div>

       <div class="row mb-3">
            <div class="col-md-6">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="nombre" name="nombre"
                    pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$"
                    title="Solo se permiten letras y espacios. Sin números ni símbolos." required>
                    </div>
                    <div class="col-md-6">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" class="form-control" id="marca" name="marca" required>
            </div>
        </div>


        <div class="row mb-3">
          <div class="col-md-6">
            <label for="categoria" class="form-label">Categoría</label>
            <select class="form-select" id="categoria" name="categoria" required>
              <option selected disabled value="">Seleccione la categoría</option>
              <option value="PC Escritorio Gaming">PC Escritorio Gaming</option>
              <option value="PC Escritorio Uso General">PC Escritorio Uso General</option>
              <option value="Laptop Gaming">Laptop Gaming</option>
              <option value="Laptop Uso General">Laptop Uso General</option>
              <option value="Pieza PC">Pieza PC</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="modelo" class="form-label">Modelo</label>
            <input type="text" class="form-control" id="modelo" name="modelo" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="estado" class="form-label">Estado</label>
            <select class="form-select" id="estado" name="estado" required>
              <option selected disabled value="">Seleccione el estado</option>
              <option value="Nuevo">Nuevo</option>
              <option value="Usado">Usado</option>
            </select>
          </div>
          <div class="col-md-6">
            <label for="inventario_minimo" class="form-label">Inventario mínimo</label>
            <input type="number" class="form-control" id="inventario_minimo" name="inventario_minimo" min="0" required>
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label for="precio_compra" class="form-label">Precio de Compra</label>
            <input type="number" class="form-control" step="0.01" id="precio_compra" name="precio_compra" required>
          </div>
          <div class="col-md-6">
            <label for="tipo_almacenamiento" class="form-label">Tipo de Almacenamiento</label>
            <select class="form-select" id="tipo_almacenamiento" name="tipo_almacenamiento" required>
              <option selected disabled value="">Seleccione el tipo</option>
              <option value="Bodega">Bodega</option>
              <option value="Vitrina">Vitrina</option>
            </select>
          </div>
        </div>

        <div class="mb-3">
            <label for="tiempo_garantia" class="form-label">Tiempo de Garantía</label>
            <input type="text" class="form-control" id="tiempo_garantia" name="tiempo_garantia"
            placeholder="Ingresar los meses o años de garantia" required>
        </div>

        <div class="mb-3">
          <label for="fecha" class="form-label">Fecha</label>
          <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>

          <!--  Mustra los botones de el formulario principal -->
        <div class="text-center">
          <div class="text-center">
          <button type="submit" name="guardar"
            class="btn <?php echo (isset($_POST['modo_edicion']) && $_POST['modo_edicion'] == '1') ? 'btn-primary' : 'btn-success'; ?> fw-bold shadow-sm"
            style="transition: transform 0.2s ease, box-shadow 0.2s ease;"
            onmouseover="this.style.transform='scale(1.03)'"
            onmouseout="this.style.transform='scale(1)'"
            onmousedown="this.style.transform='scale(0.97)'"
            onmouseup="this.style.transform='scale(1.03)'">
           <i class="bi <?php echo (isset($_POST['modo_edicion']) && $_POST['modo_edicion'] == '1') ? 'bi-pencil-square' : 'bi-plus-lg'; ?>"></i>
           <?php echo (isset($_POST['modo_edicion']) && $_POST['modo_edicion'] == '1') ? 'Actualizar' : 'Insertar'; ?>
          </button>
          <button type="reset"
    class="btn btn-warning fw-bold shadow-sm ms-2"
    style="transition: transform 0.2s ease, box-shadow 0.2s ease;"
    onmouseover="this.style.transform='scale(1.03)'"
    onmouseout="this.style.transform='scale(1)'"
    onmousedown="this.style.transform='scale(0.97)'"
    onmouseup="this.style.transform='scale(1.03)'">
    Limpiar
  </button>
</div>
        </div>
      </form>

      <!-- Muestra el registro de los datos guardados-->
        <?php
              $mensaje = "";
              $conn = new mysqli("localhost", "root", "", "proyectosolo");

              if ($conn->connect_error) {
                  die("Conexión fallida: " . $conn->connect_error);
              }

              // Eliminar producto si se solicitó
              if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_id'])) {
                  $idEliminar = (int) $_POST['eliminar_id'];
                  $conn->query("DELETE FROM t_productos WHERE id_producto = $idEliminar");
              }

              // Filtro de búsqueda por código
              $condicion = "";
              if (isset($_GET['buscar_codigo']) && !empty($_GET['buscar_codigo'])) {
                  $buscar = $conn->real_escape_string($_GET['buscar_codigo']);
                  $condicion = "WHERE p.codigo LIKE '%$buscar%'";
              }

              // Consulta principal
              $consulta = "
                  SELECT 
                      p.id_producto,
                      p.codigo,
                      p.nombre,
                      e.marca,
                      e.modelo,
                      p.categoria,
                      p.estado,
                      p.fecha,
                      p.inventario_minimo,
                      p.precio_compra,
                      p.tipo_almacenamiento,
                      g.Tiempo AS garantia
                  FROM t_productos p
                  LEFT JOIN t_especificaciones e ON p.id_marca = e.id_marca
                  LEFT JOIN t_garantia g ON p.Id_Garantia = g.Id_Garantia
                  $condicion
                  ORDER BY p.id_producto DESC
              ";

              $resultado = $conn->query($consulta);
              ?>

              <!DOCTYPE html>
              <html lang="es">
              <head>
                  <meta charset="UTF-8">
                  <title>Productos Registrados</title>
                  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
              </head>
              <body class="p-4 bg-light">

              <div class="container">
                  <h2 class="mb-4 text-center"> Productos Registrados</h2>

                  <!-- Formulario de búsqueda -->
                  <form method="GET" class="mb-4">
                    <div class="input-group">
                    <input type="text" name="buscar_codigo" class="form-control" placeholder="Buscar por código de producto..." value="<?= isset($_GET['buscar_codigo']) ? htmlspecialchars($_GET['buscar_codigo']) : '' ?>">
                    <button type="submit" class="btn btn-outline-primary">Buscar</button>
                   </div>
                  </form>

          <?php if ($resultado && $resultado->num_rows > 0): ?>
          <div class="table-responsive">
              <table class="table table-bordered table-hover table-sm bg-white text-center">
                  <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Categoría</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Inventario</th>
                    <th>Precio</th>
                    <th>Almacenamiento</th>
                    <th>Garantía</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr ondblclick="editarFila(this)">
                    <td><?= $fila['id_producto'] ?></td>
                    <td><?= $fila['codigo'] ?></td>
                    <td><?= $fila['nombre'] ?></td>
                    <td><?= $fila['marca'] ?></td>
                    <td><?= $fila['modelo'] ?></td>
                    <td><?= $fila['categoria'] ?></td>
                    <td><?= $fila['estado'] ?></td>
                    <td><?= $fila['fecha'] ?></td>
                    <td><?= $fila['inventario_minimo'] ?></td>
                    <td><?= $fila['precio_compra'] ?></td>
                    <td><?= $fila['tipo_almacenamiento'] ?></td>
                    <td><?= $fila['garantia'] ?? 'Sin garantía' ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                            <input type="hidden" name="eliminar_id" value="<?= $fila['id_producto'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <div class="alert alert-warning text-center">No se encontraron productos registrados.</div>
    <?php endif; ?>
</div>

<!-- Puedes agregar aquí el script editarFila si quieres hacer doble clic para editar -->
 <script>
function editarFila(fila) {
  const c = fila.getElementsByTagName("td");
  document.getElementById("modo_edicion").value = "1";
  document.getElementById("id_producto").value = c[0].innerText;
  document.getElementById("codigo").value = c[1].innerText;
  document.getElementById("nombre").value = c[2].innerText;
  document.getElementById("marca").value = c[3].innerText;
  document.getElementById("modelo").value = c[4].innerText;
  document.getElementById("categoria").value = c[5].innerText;
  document.getElementById("estado").value = c[6].innerText;
  document.getElementById("fecha").value = c[7].innerText;
  document.getElementById("inventario_minimo").value = c[8].innerText;
  document.getElementById("precio_compra").value = c[9].innerText;
  document.getElementById("tipo_almacenamiento").value = c[10].innerText;
  document.getElementById("tiempo_garantia").value = c[11].innerText;

window.addEventListener("DOMContentLoaded", () => {
    const url = new URL(window.location.href);
    if (url.searchParams.get("buscar_codigo")) {
      const seccion = document.getElementById("resultados");
      if (seccion) {
        seccion.scrollIntoView({ behavior: "smooth" });
      }
    }
  });

}
</script>

<div class="text-end mt-3">
    <a href="registro_usuario.php"
    class="btn w-100 fw-bold mt-4 shadow-sm"
    style="background-color: #198754; color: white; transition: transform 0.2s ease, box-shadow 0.2s ease;"
    onmouseover="this.style.transform='scale(1.03)'"
    onmouseout="this.style.transform='scale(1)'"
    onmousedown="this.style.transform='scale(0.97)'"
    onmouseup="this.style.transform='scale(1.03)'"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="Registrar un nuevo usuario en el sistema">
    Agregar nuevo usuario
  </a>
</div>

<div class="text-end mt-3">
    <a href="index.php"
    class="btn w-100 fw-bold mt-4 shadow-sm"
    style="background-color: #B8860B; color: white; transition: transform 0.2s ease, box-shadow 0.2s ease;"
    onmouseover="this.style.transform='scale(1.03)'"
    onmouseout="this.style.transform='scale(1)'"
    onmousedown="this.style.transform='scale(0.97)'"
    onmouseup="this.style.transform='scale(1.03)'"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="Registrar un nuevo usuario en el sistema">
    Ir al inicio de sesion
  </a>
</div>
</html>
