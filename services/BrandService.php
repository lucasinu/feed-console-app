<?php

namespace app\services;

use app\models\Brand;
use app\repositories\BrandRepository;

class BrandService implements EntityServiceInterface
{
    private $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function findOrCreate(string $name)
    {
        $brand = $this->brandRepository->findByAttribute('name', $name) ?? new Brand();
        
        if($brand->isNewRecord) {
            $brand->name = $name;
            $this->brandRepository->save($brand);
        }
        
        return $brand;
    }
}
