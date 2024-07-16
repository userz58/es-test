<?php

namespace App\Model\Elastically;

class Category
{
    public function __construct(
        public string $code,
        public string $slug,
        public string $name,
        // tags
        // updatedAt
        // views
    )
    {
    }
}
