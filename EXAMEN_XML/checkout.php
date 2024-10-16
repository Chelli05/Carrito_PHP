<?php
session_start();

// Comprobar si hay productos en el carrito
if (empty($_SESSION['carrito'])) {
    echo "El carrito está vacío. No puedes proceder al checkout.";
    exit();
}

// Cargar stock de productos
$productosStock = simplexml_load_file('stock.xml');

//
// Cargar descuentos
$descuentos = simplexml_load_file('descuentos.xml');

// Función para calcular el total del carrito
function calcularTotal($carrito, $descuentos, $codigoDescuento = null) {
    $total = 0;
    foreach ($carrito as $id => $producto) {
        $total += $producto['precio'] * $producto['cantidad'];
    }

    // Aplicar descuento si el código es válido
    if ($codigoDescuento) {
        foreach ($descuentos->descuento as $descuento) {
            if ($descuento->codigo == $codigoDescuento) {
                $descuentoPorcentaje = (int)$descuento->porcentaje;
                $total -= ($total * ($descuentoPorcentaje / 100));
                break;
            }
        }
    }

    return $total;
}

// Procesar el formulario de checkout
if (isset($_POST['proceed'])) {
    $codigoDescuento = isset($_POST['codigo_descuento']) ? $_POST['codigo_descuento'] : null;
    $totalFinal = calcularTotal($_SESSION['carrito'], $descuentos, $codigoDescuento);
    
    // Validar stock y reducir cantidades
    foreach ($_SESSION['carrito'] as $id => $producto) {
        foreach ($productosStock->producto as $stockProducto) {
            if ($stockProducto->id == $id) {
                // Verificar si hay suficiente stock
                if ($stockProducto->stock < $producto['cantidad']) {
                    echo "No hay suficiente stock para el producto: {$producto['nombre']}. Solo quedan {$stockProducto->stock} en stock.";
                    exit();
                }
            }
        }
    }

    // Actualizar stock
    foreach ($_SESSION['carrito'] as $id => $producto) {
        foreach ($productosStock->producto as $stockProducto) {
            if ($stockProducto->id == $id) {
                $stockProducto->stock -= $producto['cantidad'];
            }
        }
    }
    $productosStock->asXML('stock.xml'); // Guardar cambios en el XML

    // Mostrar resumen de compra
    echo "<h1>Resumen de Compra</h1>";
    echo "<p>Total a pagar: $$totalFinal</p>";
    echo "<p>¡Gracias por su compra!</p>";
    
    // Vaciar el carrito después de la compra
    $_SESSION['carrito'] = [];
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>

<h1>Checkout</h1>

<!-- Formulario para aplicar código de descuento -->
<form method="POST" action="">
    <label for="codigo_descuento">Código de Descuento:</label>
    <input type="text" name="codigo_descuento" id="codigo_descuento">
    <button type="submit" name="proceed">Proceder al Pago</button>
</form>

<!-- Mostrar contenido del carrito -->
<h2>Carrito de Compras</h2>
<table border="1">
    <tr>
        <th>Producto</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Total</th>
    </tr>
    <?php foreach ($_SESSION['carrito'] as $id => $producto): ?>
    <tr>
        <td><?php echo $producto['nombre']; ?></td>
        <td><?php echo $producto['precio']; ?></td>
        <td><?php echo $producto['cantidad']; ?></td>
        <td><?php echo $producto['precio'] * $producto['cantidad']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
