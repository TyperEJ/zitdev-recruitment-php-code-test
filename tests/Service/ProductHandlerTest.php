<?php

namespace Test\Service;

use PHPUnit\Framework\TestCase;
use App\Service\ProductHandler;

/**
 * Class ProductHandlerTest
 */
class ProductHandlerTest extends TestCase
{
    private $products = [
        [
            'id' => 1,
            'name' => 'Coca-cola',
            'type' => 'Drinks',
            'price' => 10,
            'create_at' => '2021-04-20 10:00:00',
        ],
        [
            'id' => 2,
            'name' => 'Persi',
            'type' => 'Drinks',
            'price' => 5,
            'create_at' => '2021-04-21 09:00:00',
        ],
        [
            'id' => 3,
            'name' => 'Ham Sandwich',
            'type' => 'Sandwich',
            'price' => 45,
            'create_at' => '2021-04-20 19:00:00',
        ],
        [
            'id' => 4,
            'name' => 'Cup cake',
            'type' => 'Dessert',
            'price' => 35,
            'create_at' => '2021-04-18 08:45:00',
        ],
        [
            'id' => 5,
            'name' => 'New York Cheese Cake',
            'type' => 'Dessert',
            'price' => 40,
            'create_at' => '2021-04-19 14:38:00',
        ],
        [
            'id' => 6,
            'name' => 'Lemon Tea',
            'type' => 'Drinks',
            'price' => 8,
            'create_at' => '2021-04-04 19:23:00',
        ],
    ];

    public function testGetTotalPrice()
    {
        $productHandler = new ProductHandler($this->products);

        $this->assertEquals(143, $productHandler->getTotalPrice());
    }

    public function testGetDessertProducts()
    {
        $productHandler = new ProductHandler($this->products);

        $this->assertEquals([
            [
                'id' => 5,
                'name' => 'New York Cheese Cake',
                'type' => 'Dessert',
                'price' => 40,
                'create_at' => '2021-04-19 14:38:00',
            ],
            [
                'id' => 4,
                'name' => 'Cup cake',
                'type' => 'Dessert',
                'price' => 35,
                'create_at' => '2021-04-18 08:45:00',
            ],
        ], $productHandler->getDessertProducts());
    }

    public function testCreateTimeToTimestamp()
    {
        $productHandler = new ProductHandler($this->products);

        $this->assertEquals([
            [
                'id' => 1,
                'name' => 'Coca-cola',
                'type' => 'Drinks',
                'price' => 10,
                'create_at' => 1618912800,
            ],
            [
                'id' => 2,
                'name' => 'Persi',
                'type' => 'Drinks',
                'price' => 5,
                'create_at' => 1618995600,
            ],
            [
                'id' => 3,
                'name' => 'Ham Sandwich',
                'type' => 'Sandwich',
                'price' => 45,
                'create_at' => 1618945200,
            ],
            [
                'id' => 4,
                'name' => 'Cup cake',
                'type' => 'Dessert',
                'price' => 35,
                'create_at' => 1618735500,
            ],
            [
                'id' => 5,
                'name' => 'New York Cheese Cake',
                'type' => 'Dessert',
                'price' => 40,
                'create_at' => 1618843080,
            ],
            [
                'id' => 6,
                'name' => 'Lemon Tea',
                'type' => 'Drinks',
                'price' => 8,
                'create_at' => 1617564180,
            ],
        ], $productHandler->createTimeToTimestamp());
    }
}