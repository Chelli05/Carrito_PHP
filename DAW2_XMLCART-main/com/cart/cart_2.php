<?php

include_once("com/catalog/catalog.php");

$cart_file='com/xmldb/cart.xml';
//////////////////////////////////////////////////////
function AddToCart($id_prod, $quantity){

    echo "AddToCart <br>";
    echo $id_prod . "<br>";

    if (ExistProduct($id_prod, $quantity)){
        _ExecuteAddToCart($id_prod, $quantity);
    }else{
        echo "No hay suficiente producto <br/>";
    };
    _ExecuteAddToCart($id_prod, $quantity);
}
//////////////////////////////////////////////////////
function _ExecuteAddToCart($id_prod, $quantity){

    $cart_file = 'com/xmldb/cart.xml';

    $cart = GetCart();
  
    $item = $cart->addChild('product_item');

    $item->addChild('id_product', $id_prod);
    $item->addChild('quantity', $quantity);
    
    $item_price = $item->addChild('price_item');
    $item_price->addChild('price', '0');
    $item_price->addChild('currency', 'EU');

    $cart->asXML($cart_file);
}

//////////////////////////////////////////////////////
function GetCart() {
    $cart_file = 'com/xmldb/cart.xml';

    if (file_exists($cart_file) && filesize($cart_file) > 0) {
        echo "El archivo del carrito existe y no está vacío.<br/>";
        $cart = simplexml_load_file($cart_file);
    } else {
        echo "El archivo del carrito no existe o está vacío. Creando uno nuevo.<br>";
        // Crear un nuevo XML para el carrito
        $cart = new SimpleXMLElement('<cart></cart>');
        // Guardar el carrito vacío en el archivo
        $cart->asXML($cart_file);
    }

    return $cart;
}

/////////////////////////////////////////////////////


echo "INIT EXECUTION<br><br>";

include_once("com/cart/cart.php");
include_once("com/catalog/catalog.php");

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'add_to_cart' && isset($_GET['id_prod']) && isset($_GET['quantity'])) {
        $id_prod = $_GET['id_prod'];
        $quantity = $_GET['quantity'];
        AddToCart($id_prod, $quantity);
    } elseif ($action == 'validate_user' && isset($_GET['username']) && isset($_GET['password'])) {
        $username = $_GET['username'];
        $password = $_GET['password'];
        if (ValidateUser($username, $password)) {
            echo "Usuario válido.<br/>";
        } else {
            echo "Usuario o contraseña incorrectos.<br/>";
        }
    } else {
        echo "Acción no válida o parámetros faltantes.<br/>";
    }
} else {
    echo "No se ha especificado ninguna acción.<br/>";
}


?>