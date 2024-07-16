<?php

namespace App\Model\Elastically;

class Product
{
    public function __construct(
        public string $code,
        public string $sku,
        public string $name,
        public array  $categories = [],//dto
        public array  $values = [],
        // tags
        // updatedAt
        // countViews
        // countOrdered
        // countAddedToShoppingCart
    )
    {
        // todo: проверка типов webmozart asserts
    }
}
