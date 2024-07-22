<?php

namespace App\Model\Elastically;

class Product
{
    public function __construct(
        public string             $name,
        public ?string            $parent,
        public string             $code,
        public string             $sku,
        public array              $slugs,
        public array              $categories,
        public array              $values,
        public bool               $enabled,
        public ?string            $image,
        public ?string            $familyCode,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
        public int $counterViews = 0,
        public int $counterOrders = 0,
        public int $counterCarts = 0,
        // tags
    )
    {
        // todo: проверка типов webmozart asserts
    }
}
