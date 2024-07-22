<?php

namespace App\Builder\Search;

use App\Entity\CatalogCategory;
use App\Entity\CatalogProduct;
use App\Model\Elastically\Category;
use App\Model\Elastically\Product;

class ProductModelBuilder
{
    public function create(CatalogProduct $product): Product
    {
        // только активные
        //$categories = $product->getCategories()->filter(fn($category) => $category->isEnabled());

        $slugs = [];
        $categories = [];
        foreach ($product->getCategories() as $category) {
            /** @var CatalogCategory $category */
            if (false === $category->isEnabled()) {
                continue;
            }

            $categories[] = new Category(
                $category->getName(),
                $category->getSlug(),
                $category->isEnabled(),
                $category->getId(),
                $category->getParent()->getId(),
                $category->getRoot()->getId(),
                $category->getLeft(),
                $category->getRight(),
                $category->getLevel(),
            // tags
            //views
            );

            $slugs[] = sprintf('%s/%s', $category->getSlug(), $product->getSlug());
        }

        return new Product(
            $product->getName(),
            $product->getParent(),
            $product->getCode(),
            $product->getSku(),
            $slugs,
            $categories,
            $product->getValues(),
            $product->isEnabled(),
            $product->getImage(),
            $product->getFamily()->getCode(),
            $product->getCreatedAt(),
            $product->getUpdatedAt(),
            rand(100, 999),
            rand(10, 99),
            rand(10, 99),
        // tags
        // updatedAt
        // countViews
        // countOrdered
        // countAddedToShoppingCart
        );
    }
}
