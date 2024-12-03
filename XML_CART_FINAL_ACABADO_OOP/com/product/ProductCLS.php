<?php

class clsProduct {
    public int $id;
    public string $name;
    public float $price;
    public int $stock;
    public float $priceWithVat; 

    public function __construct(int $id, string $name, float $price, int $stock, float $priceWithVat) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->priceWithVat = $priceWithVat; 
    }
}
?>
