<?php

namespace App\Service;

class ProductHandler
{
    protected $products = [];

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function getTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->products as $product) {
            $price = $product['price'] ?: 0;
            $totalPrice += $price;
        }

        return $totalPrice;
    }

    public function getDessertProducts()
    {
        $dessertProducts = array_filter($this->products, function ($product) {
            $type = $product['type'] ?: '';

            return strtolower($type) === 'dessert';
        });

        usort($dessertProducts, function ($dessertProductPrev, $dessertProductNext) {
            $prevPrice = $dessertProductPrev['price'] ?: 0;
            $nextPrice = $dessertProductNext['price'] ?: 0;

            if ($prevPrice === $nextPrice) return 0;

            return $prevPrice > $nextPrice ? -1 : 1;
        });

        return $dessertProducts;
    }

    public function createTimeToTimestamp()
    {
        return array_map(function ($product) {
            $createTime = $product['create_at'] ?: null;

            $product['create_at'] = $createTime ? strtotime($createTime) : null;

            return $product;
        }, $this->products);
    }
}