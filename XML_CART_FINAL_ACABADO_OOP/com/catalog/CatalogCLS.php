<?php

require_once('com/product/ProductCLS.php');

class CLSCatalog {
    private string $catalogFile;
    private float $vatRate = 0.21;

    public function __construct(string $catalogFile) {
        $this->catalogFile = $catalogFile;
    }

    public function viewCatalog(): SimpleXMLElement {
        return $this->loadCatalog();
    }

    public function getProductsAsArray(): array {
        $catalog = $this->loadCatalog();
        $products = [];

        foreach ($catalog->product_item as $item) {
            
            $priceWithVat = (float)$item->price * (1 + $this->vatRate);

            
            $products[] = new clsProduct(
                (int)$item->id_product,
                (string)$item->name,
                (float)$item->price,
                (int)$item->stock,
                $priceWithVat 
            );
        }

        return $products;
    }

    public function loadCatalog(): SimpleXMLElement {
        if (file_exists($this->catalogFile)) {
            return simplexml_load_file($this->catalogFile);
        }
        throw new Exception('Catálogo no encontrado');
    }

    public function getProductPrice(int $productId): ?float {
        $catalog = $this->loadCatalog();
        foreach ($catalog->product_item as $item) {
            if ((int)$item->id_product === $productId) {
                return (float)$item->price;
            }
        }
        return null;
    }

    public function getProductPriceWithVat(int $productId): ?float {
        $catalog = $this->loadCatalog();
        foreach ($catalog->product_item as $item) {
            if ((int)$item->id_product === $productId) {
                return (float)$item->price * (1 + $this->vatRate);
            }
        }
        return null;
    }
}

?>
