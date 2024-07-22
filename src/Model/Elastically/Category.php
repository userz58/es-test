<?php

namespace App\Model\Elastically;

class Category
{
    public function __construct(
        public string   $name,
        public string   $slug,
        public bool     $enabled,
        public int      $id,
        public int|null $parentId,
        public int      $root,
        public int      $left,
        public int      $right,
        public int      $level,
        // tags
        // updatedAt
        // views
    )
    {
    }
}
