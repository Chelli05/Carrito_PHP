<?php

require_once('com/catalog/CatalogCLS.php');
require_once('com/product/ProductCLS.php');
class CLSCart {
    private string $cartFile;

    public function __construct(string $username) {
        $this->cartFile = 'xmldb/cart_info/' . $username . '_cart.xml';
        $this->createEmptyCart();
    }

    public function createEmptyCart(): void {
        if (!file_exists($this->cartFile)) {
            $cart = new SimpleXMLElement('<cart></cart>');
            $cart->asXML($this->cartFile);
        }
    }

    public function addToCart(int $productId, int $quantity): void {
        $cart = $this->loadCart();
        $productAdded = false;

        foreach ($cart->product_item as $item) {
            if ((int)$item->id_product === $productId) {
                $item->quantity += $quantity;
                $productAdded = true;
                break;
            }
        }

        if (!$productAdded) {
            $product = $cart->addChild('product_item');
            $product->addChild('id_product', $productId);
            $product->addChild('quantity', $quantity);
        }

        $cart->asXML($this->cartFile);
    }

    function viewCart(string $discountCode = null): SimpleXMLElement {
        $cart = $this->loadCart();
        $catalog = new CLSCatalog('xmldb/catalog.xml');
        $discount = $this->validateDiscount($discountCode);
    
        $total = 0;
        $vatRate = 0.21;
    
        foreach ($cart->product_item as $item) {
            $productId = (int)$item->id_product;
            $price = $catalog->getProductPrice($productId);
    
            $priceWithVAT = $price * (1 + $vatRate);
    
            $item->addChild('price_with_vat', number_format($priceWithVAT, 2, '.', ''));
    
            $total += $priceWithVAT * (int)$item->quantity;
        }
    
        if ($discount) {
            $total -= $total * $discount;
        }
    
        $cart->addChild('total', number_format($total, 2, '.', ''));
        if ($discountCode) {
            $cart->addChild('discount_code', $discountCode);
            $cart->addChild('discount_value', $discount);
        }
    
        return $cart;
    }
    

    private function loadCart(): SimpleXMLElement {
        if (file_exists($this->cartFile)) {
            return simplexml_load_file($this->cartFile);
        }
        $cart = new SimpleXMLElement('<cart></cart>');
        $cart->asXML($this->cartFile);
        return $cart;
    }

    private function validateDiscount(?string $discountCode): ?float {
        if (!$discountCode) {
            return null;
        }

        $discountsFile = 'xmldb/discounts/discounts.xml';
        if (!file_exists($discountsFile)) {
            throw new Exception('Archivo de descuentos no encontrado');
        }

        $discounts = simplexml_load_file($discountsFile);
        foreach ($discounts->discount as $discount) {
            if ((string)$discount->code === $discountCode) {
                return (float)$discount->value;
            }
        }

        throw new Exception('Código de descuento inválido');
    }
}

?>
