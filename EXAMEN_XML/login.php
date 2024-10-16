<?php
session_start(); // Iniciar sesión para guardar la autenticación

// Comprobar si se envió el formulario
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cargar el archivo users.xml
    if (file_exists('users.xml')) {
        $users = simplexml_load_file('users.xml');

        // Validar el usuario y contraseña
        $isValidUser = false;
        foreach ($users->user as $user) {
            if ($user->username == $username && $user->password == $password) {
                $isValidUser = true;
                break;
            }
        }

        if ($isValidUser) {
            // Usuario y contraseña correctos, iniciar sesión
            $_SESSION['username'] = $username; // Guardar el nombre de usuario en la sesión
            header('Location: cart.php'); // Redirigir al carrito
            exit();
        } else {
            $error = "Nombre de usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Archivo de usuarios no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>

    <!-- Mostrar mensaje de error si hay alguno -->
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Formulario de login -->
    <form method="POST" action="login.php">
        <label for="username">Nombre de Usuario:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit" name="login">Iniciar Sesión</button>
    </form>

</body>
</html>
