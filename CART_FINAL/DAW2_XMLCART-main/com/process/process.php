<?php

// Comprobar si se han pasado los parámetros en la URL
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Si la acción es "add_to_cart"
    if ($action == 'add_to_cart') {
        // Obtener los parámetros necesarios de la URL
        $id_prod = isset($_GET['id_prod']) ? $_GET['id_prod'] : null;
        $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

        // Verificar que se han recibido ambos parámetros
        if ($id_prod !== null) {
            AddToCart($id_prod, $quantity); // Llamar a la función para añadir al carrito
        } else {
            echo "ID del producto no especificado.<br>";
        }
    }

    // Si la acción es "add_to_catalog"
    elseif ($action == 'add_to_catalog') {
        // Obtener los parámetros necesarios de la URL
        $id_prod = isset($_GET['id_prod']) ? $_GET['id_prod'] : null;
        $quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 0;
        $price = isset($_GET['price']) ? (float)$_GET['price'] : 0.0;

        // Verificar que se han recibido todos los parámetros necesarios
        if ($id_prod !== null && $quantity > 0 && $price > 0) {
            AddProductToCatalog($id_prod, $quantity, $price, 'EU'); // Llamar a la función para añadir al catálogo
        } else {
            echo "ID del producto, cantidad o precio no especificados correctamente.<br>";
        }
    }

    // Otras acciones pueden ir aquí...

} else {
    echo "No se ha especificado ninguna acción.<br>"; // Mensaje por defecto
}

?>
