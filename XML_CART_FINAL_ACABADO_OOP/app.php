<?php
session_start();

// Incluir las clases necesarias para el archivo principal
require_once('com/cart/CartCLS.php');
require_once('com/catalog/CatalogCLS.php');
require_once('com/user/UsersCLS.php');
require_once('com/product/ProductCLS.php');

// Archivos de datos
$userFile = 'xmldb/users.xml';
$catalogFile = 'xmldb/catalog.xml';
$discountsFile = 'xmldb/discounts.xml';

// Instanciar las clases
$user = new CLSUser($userFile);
$catalog = new CLSCatalog($catalogFile);
$cart = isset($_SESSION['username']) ? new CLSCart($_SESSION['username']) : null;

function handleRequest() {
    global $user, $catalog, $cart;

    $action = $_GET['action'] ?? null;
    $username = $_GET['username'] ?? null;
    $password = $_GET['password'] ?? null;
    $productId = $_GET['productId'] ?? null;
    $quantity = $_GET['quantity'] ?? null;
    $discountCode = $_GET['discountCode'] ?? null;

    try {
        switch ($action) {
            case 'login':
                if ($username && $password) {
                    if ($user->login($username, $password)) {
                        $_SESSION['username'] = $username;
                        $cart = new CLSCart($username);
                        sendResponse(['message' => 'Inicio de sesión exitoso']);
                    } else {
                        sendResponse(['error' => 'Credenciales inválidas']);
                    }
                } else {
                    sendResponse(['error' => 'Faltan parámetros']);
                }
                break;

            case 'logout':
                session_destroy();
                sendResponse(['message' => 'Sesión cerrada']);
                break;

            case 'register':
                if ($username && $password) {
                    $user->register($username, $password);
                    sendResponse(['message' => 'Usuario registrado']);
                } else {
                    sendResponse(['error' => 'Faltan parámetros']);
                }
                break;

            case 'addToCart':
                if (isUserAuthenticated() && $cart && $productId && $quantity) {
                    $cart->addToCart((int)$productId, (int)$quantity);
                    sendResponse(['message' => 'Producto añadido']);
                } else {
                    sendResponse(['error' => 'Parámetros insuficientes o usuario no autenticado']);
                }
                break;

            case 'viewCart':
                if (isUserAuthenticated() && $cart) {
                    sendResponse($cart->viewCart($discountCode), 'xml');
                } else {
                    sendResponse(['error' => 'Carrito no disponible o usuario no autenticado']);
                }
                break;

            case 'catalog':
                sendResponse($catalog->viewCatalog(), 'xml');
                break;

            default:
                sendResponse(['error' => 'No se han introducido parametros en la URL']);
        }
    } catch (Exception $e) {
        sendResponse(['error' => $e->getMessage()]);
    }
}

function sendResponse($data, $format = 'xml') {
    header('Content-Type: application/xml');
    
    if ($data instanceof SimpleXMLElement) {
        echo $data->asXML();
    } else {
        $xml = new SimpleXMLElement('<response/>');
        arrayToXml($data, $xml);
        echo $xml->asXML();
    }
}

function arrayToXml($data, SimpleXMLElement $xml) {
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $subnode = $xml->addChild($key);
            arrayToXml($value, $subnode);
        } else {
            $xml->addChild($key, htmlspecialchars($value));
        }
    }
}


function isUserAuthenticated(): bool {
    return isset($_SESSION['username']);
}
?>
