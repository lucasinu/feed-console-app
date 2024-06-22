<?php

namespace app\services;

use app\models\Product;
use app\repositories\ProductRepository;
use SimpleXMLElement;

class ProductService implements ProductServiceInterface
{
    private $productRepository;
    private $categoryService;
    private $brandService;

    public function __construct(
        ProductRepository $productRepository,
        CategoryService $categoryService,
        BrandService $brandService
    ) {
        $this->productRepository = $productRepository;
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;
    }

    public function processProduct(SimpleXMLElement $node): Product
    {
        // Check if the product exists to avoid duplicates
        $product = $this->productRepository->findByAttribute('external_id', (int)$node->entity_id) ?? new Product();
        
        // Updating all the product's attributes
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
    
    public function processProductNode(SimpleXMLElement $node, &$log_data, &$new_items): Product
    {
        $product = $this->processProduct($node);
        $new = $product->isNewRecord;

        if ((string)$node->Brand != '') {
            $brand = $this->brandService->findOrCreate((string)$node->Brand);
            if ($brand->idbrand) {
                $product->brand_idbrand = $brand->idbrand;
            } else {
                $log_data[] = ['import', 'error', 'brand', $brand->name, json_encode($brand->errors)];
                print_r('Brand with name "' . $brand->name . '" not saved! See the log file for further information.');
            }
        }

        if ((string)$node->CategoryName != '') {
            $category = $this->categoryService->findOrCreate((string)$node->CategoryName);
            if ($category->idcategory) {
                $product->category_idcategory = $category->idcategory;
            } else {
                $log_data[] = ['import', 'error', 'category', $category->name, json_encode($category->errors)];
                print_r('Category with name "' . $category->name . '" not saved! See the log file for further information.');
            }
        }

        if (!$product->save()) {
            $log_data[] = ['import', 'error', 'product', $product->external_id, json_encode($product->errors)];
            print_r('Product with external_id "' . $product->external_id . '" not saved! See the log file for further information.');
        } else {
            $log_data[] = ['import', 'info', 'product', $product->external_id, 'Product ' . ($new ? 'added!' : 'updated!')];
            if ($new) {
                $new_items++;
            }
        }

        return $product;
    }
}
