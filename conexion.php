<?php
// conexion.php
$servername = "localhost"; // O la IP de tu servidor de BD
$username = "root";       // <-- ¡CAMBIA ESTO! Tu usuario de la base de datos
$password = "";           // <-- ¡CAMBIA ESTO! Tu contraseña de la base de datos
$dbname = "proyectosolo"; // <-- ¡CAMBIA ESTO! El nombre de tu base de datos

// Crear conexión (usando la sintaxis orientada a objetos)
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) { // Usando $conn->connect_error para la forma orientada a objetos
    die("Conexión fallida: " . $conn->connect_error);
}

// Opcional: Establecer el conjunto de caracteres a UTF-8 para evitar problemas con acentos y ñ
$conn->set_charset("utf8"); // Usando $conn->set_charset para la forma orientada a objetos

// Nota: No necesitas cerrar la conexión aquí, se cerrará automáticamente al final del script
// o la puedes cerrar explícitamente en login.php después de todas las operaciones de BD.
?>