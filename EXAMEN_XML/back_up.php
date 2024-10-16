<?php
session_start(); // Iniciar sesión para el manejo del carrito

// Cargar stock de productos
$productosStock = simplexml_load_file('stock.xml');

// Cargar descuentos
$descuentos = simplexml_load_file('descuentos.xml');

// Inicializar el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Función para agregar un producto al carrito
function agregarAlCarrito($productoID, $nombreProducto, $precioProducto, $cantidad) {
    if (isset($_SESSION['carrito'][$productoID])) {
        $_SESSION['carrito'][$productoID]['cantidad'] += $cantidad; // Sumar a la cantidad existente
    } else {
        // Agregar un nuevo producto al carrito
        $_SESSION['carrito'][$productoID] = [
            'nombre' => $nombreProducto,
            'precio' => $precioProducto,
            'cantidad' => $cantidad
        ];
    }
}

// Función para eliminar un producto del carrito
function eliminarDelCarrito($productoID) {
    unset($_SESSION['carrito'][$productoID]);
}

// Función para actualizar la cantidad de un producto en el carrito
function actualizarCantidad($productoID, $nuevaCantidad) {
    if ($nuevaCantidad > 0) {
        $_SESSION['carrito'][$productoID]['cantidad'] = $nuevaCantidad;
    } else {
        eliminarDelCarrito($productoID); // Eliminar el producto si la cantidad es 0
    }
}

// Función para vaciar el carrito
function vaciarCarrito() {
    $_SESSION['carrito'] = []; // Vaciar todo el carrito
}

// Función para guardar el carrito en XML
function guardarCarrito($username) {
    $carritoFile = 'carritos/' . $username . '_carrito.xml'; // Ruta del archivo XML
    $carritoXML = new SimpleXMLElement('<carrito/>');

    // Iterar sobre los productos en el carrito y agregarlos al XML
    foreach ($_SESSION['carrito'] as $id => $producto) {
        $productoXML = $carritoXML->addChild('producto');
        $productoXML->addChild('id', $id);
        $productoXML->addChild('nombre', $producto['nombre']);
        $productoXML->addChild('precio', $producto['precio']);
        $productoXML->addChild('cantidad', $producto['cantidad']);
    }

    // Guardar el archivo XML
    $carritoXML->asXML($carritoFile);
}

// Función para cargar el carrito desde XML
function cargarCarrito($username) {
    $carritoFile = 'carritos/' . $username . '_carrito.xml'; // Ruta del archivo XML
    if (file_exists($carritoFile)) {
        $carritoXML = simplexml_load_file($carritoFile); // Cargar el XML
        foreach ($carritoXML->producto as $producto) {
            // Agregar cada producto al carrito de la sesión
            agregarAlCarrito((string)$producto->id, (string)$producto->nombre, (float)$producto->precio, (int)$producto->cantidad);
        }
    }
}

// Procesar formulario de agregar productos
if (isset($_POST['agregar'])) {
    $productoID = $_POST['producto_id'];
    $nombreProducto = $_POST['nombre'];
    $precioProducto = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    agregarAlCarrito($productoID, $nombreProducto, $precioProducto, $cantidad);
}

// Procesar formulario de eliminar productos
if (isset($_POST['eliminar'])) {
    $productoID = $_POST['producto_id'];
    eliminarDelCarrito($productoID);
}

// Procesar formulario de actualizar cantidades
if (isset($_POST['actualizar'])) {
    $productoID = $_POST['producto_id'];
    $nuevaCantidad = $_POST['nueva_cantidad'];
    actualizarCantidad($productoID, $nuevaCantidad);
}

// Procesar formulario para vaciar el carrito
if (isset($_POST['vaciar_carrito'])) {
    vaciarCarrito();
}

// Redirigir a checkout
if (isset($_POST['checkout'])) {
    if (isset($_SESSION['username'])) {
        guardarCarrito($_SESSION['username']); // Guardar el carrito al proceder al checkout
    }
    header('Location: checkout.php');
    exit();
}

// Cargar el carrito si el usuario ha iniciado sesión
if (isset($_SESSION['username'])) {
    cargarCarrito($_SESSION['username']); // Cargar el carrito desde el XML
}

// Al acceder al carrito, guardar el carrito actual (si existe) en XML
if (isset($_SESSION['username']) && !empty($_SESSION['carrito'])) {
    guardarCarrito($_SESSION['username']); // Guardar el carrito automáticamente
}

// Al cerrar sesión
if (isset($_POST['logout'])) {
    if (isset($_SESSION['username'])) {
        guardarCarrito($_SESSION['username']); // Guardar el carrito al cerrar sesión
    }
    session_destroy(); // Destruir la sesión
    header("Location: login.php"); // Redirigir a login.php
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
</head>
<body>

    <h1>Bienvenido al Carrito de Compras</h1>

    <!-- Formulario para añadir productos -->
    <h2>Productos Disponibles</h2>

    <?php foreach ($productosStock->producto as $producto): ?>
    <form method="POST" action="">
        <div>
            <p><?php echo $producto->nombre; ?> - $<?php echo $producto->precio; ?></p>
            <input type="hidden" name="producto_id" value="<?php echo $producto->id; ?>">
            <input type="hidden" name="nombre" value="<?php echo $producto->nombre; ?>">
            <input type="hidden" name="precio" value="<?php echo $producto->precio; ?>">
            <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto->stock; ?>">
            <button type="submit" name="agregar">Agregar al carrito</button>
        </div>
    </form>
    <?php endforeach; ?>

    <!-- Mostrar contenido del carrito -->
    <h2>Carrito de Compras</h2>
    <?php if (!empty($_SESSION['carrito'])): ?>
        <table border="1">
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Total</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($_SESSION['carrito'] as $id => $producto): ?>
            <tr>
                <td><?php echo $producto['nombre']; ?></td>
                <td><?php echo $producto['precio']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                        <input type="number" name="nueva_cantidad" value="<?php echo $producto['cantidad']; ?>" min="1">
                        <button type="submit" name="actualizar">Actualizar</button>
                    </form>
                </td>
                <td><?php echo $producto['precio'] * $producto['cantidad']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="producto_id" value="<?php echo $id; ?>">
                        <button type="submit" name="eliminar">Eliminar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <!-- Formulario para vaciar el carrito -->
        <form method="POST" action="">
            <button type="submit" name="vaciar_carrito">Vaciar Carrito</button>
        </form>

        <!-- Botón para proceder al checkout -->
        <form method="POST" action="">
            <button type="submit" name="checkout">Proceder al Checkout</button>
        </form>
    <?php else: ?>
        <p>El carrito está vacío.</p>
    <?php endif; ?>

    <br>
    <br>
    <form method="POST" action="">
        <button type="submit" name="logout">Cerrar sesión</button>
    </form>

</body>
</html>