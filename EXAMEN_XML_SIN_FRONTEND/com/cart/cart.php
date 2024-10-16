<?php
/////////////////////////////////////////////////////

function AddToCart($id_producto, $cantidad){


    echo "add to cart" . "<br>";
    echo $id_producto . "<br>";    

    if (ExistProduct($id_producto)){
        _ExecuteAddToCart($id_producto, $cantidad);
    } else {
        echo 'No hay suficiente producto';
    }

}

/////////////////////////////////////////////////////
function GetCart(){

    $file = 'XML_DB/cart.xml';
    
    if (file_exists($file)) {
        
        $cart = simplexml_load_file($file);
    
    } else {
    
    $cart = new SimpleXMLElement('<cart></cart>');
    }

    return $cart;
}

/////////////////////////////////////////////////////
function _ExecuteAddToCart($id_producto, $cantidad){

    echo "add to cart" . "<br>";
    echo $id_producto . "<br>";    

    $cart = GetCart();    
    
    $item = $cart->addChild('product_item');
    
    $cart->addChild('id_producto', $id_producto);
    $cart->addChild('cantidad', $cantidad);
    
    $item_price=$item->addChild('price_item');

    $item_price->addChild('precio', '0');
    $item_price->addChild('divisa', 'EU');

    $cart->asXML('XML_DB/cart.xml');
}


////////////////////////////////////////////////////
function UserRegister(){

}


?>