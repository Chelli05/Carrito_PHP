<?php

$catalog_file = 'Archivos PHP/DAW2_XMLCART-main/xmldb/catalog.xml';

function ExistProduct($id_prod, $quantity) {
    global $catalog_file;

    if (file_exists($catalog_file)) {
        $catalog = simplexml_load_file($catalog_file);
        foreach ($catalog->product_item as $product) {
            if ($product->id_product == $id_prod && $product->quantity >= $quantity) {
                return true;
            }
        }
    }
    return false;
}

?>
