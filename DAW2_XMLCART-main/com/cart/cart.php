<?php

function AddToCart($id_prod, $quantity) {
    if (ExistProduct($id_prod, $quantity)) {
        _ExecuteAddToCart($id_prod, $quantity);
        echo json_encode(['success' => 'Producto añadido al carrito']);
    } else {
        echo json_encode(['error' => 'Stock insuficiente para el producto ID ' . $id_prod]);
    }
}

function RemoveFromCart($id_prod) {
    global $cart_file;

    $cart = GetCart();
    foreach ($cart->product_item as $item) {
        if ((string)$item->id_product == $id_prod) {
            $dom = dom_import_simplexml($item);
            $dom->parentNode->removeChild($dom);
            $cart->asXML($cart_file);
            echo json_encode(['success' => 'Producto eliminado del carrito']);
            return;
        }
    }
    echo json_encode(['error' => 'Producto no encontrado en el carrito']);
}

function ViewCart() {
    $cart = GetCart();
    $items = [];
    foreach ($cart->product_item as $item) {
        $items[] = [
            'id_product' => (string)$item->id_product,
            'quantity' => (int)$item->quantity,
            'price' => 10 // Precio ficticio
        ];
    }
    echo json_encode(['cart' => $items]);
}

function UpdateCart($id_prod, $new_quantity) {
    global $cart_file;

    $cart = GetCart();
    foreach ($cart->product_item as $item) {
        if ((string)$item->id_product == $id_prod) {
            $item->quantity = $new_quantity;
            $cart->asXML($cart_file);
            echo json_encode(['success' => 'Cantidad actualizada']);
            return;
        }
    }
    echo json_encode(['error' => 'Producto no encontrado en el carrito']);
}

?>
