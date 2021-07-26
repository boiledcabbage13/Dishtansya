<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\Product;

class StoreOrder {
    protected $order;
    protected $product;

    public function __construct(Order $order, Product $product) {
        $this->order = $order;
        $this->product = $product;
    }

    /**
     * @param $data [product_id, quantity]
     * 
     * @return Order
     */

    public function execute(Array $data) {
        $product = $this->product->find($data['product_id']);

        if ($product->available_stock >= $data['quantity']) {
            $product->available_stock -= $data['quantity'];
            $product->save();

            return $this->order->create($data);
        }

        return false;
    }
}