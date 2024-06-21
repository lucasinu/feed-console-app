<?php

namespace app\services;

use app\models\Category;
use app\repositories\CategoryRepository;

class CategoryService implements EntityServiceInterface
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function findOrCreate(string $name)
    {
        $category = $this->categoryRepository->findByAttribute('name', $name) ?? new Category();
        
        // Check 
        if($category->isNewRecord) {
            $category->name = $name;
            $this->categoryRepository->save($category);
        }
        
        return $category;
    }
}
