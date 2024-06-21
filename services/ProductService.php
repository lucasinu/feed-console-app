<?php

namespace app\services;

use app\models\Product;
use app\repositories\ProductRepository;

class ProductService implements ProductServiceInterface
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function processProduct(\SimpleXMLElement $node): Product
    {
        $product = $this->productRepository->findByAttribute('external_id', (int)$node->entity_id) ?? new Product();
        $product->external_id = (int)$node->entity_id;
        $product->sku = (string)$node->sku;
        $product->name = (string)$node->name;
        $product->price = (float)$node->price;
        $product->link = (string)$node->link;
        $product->image = (string)$node->image;
        $product->rating = (float)$node->Rating;
        $product->count = (float)$node->Count;
        $product->caffeine_type = (string)$node->CaffeineType;
        $product->flavored = filter_var($node->Flavored, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $product->seasonal = filter_var($node->Seasonal, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $product->in_stock = filter_var($node->Instock, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $product->facebook = filter_var($node->Facebook, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $product->is_k_cup = filter_var($node->IsKCup, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        $product->short_description = (string)$node->shortdesc;
        $product->description = (string)$node->description;
        
        return $product;
    }
}
