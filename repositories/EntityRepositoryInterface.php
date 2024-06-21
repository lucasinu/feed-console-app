<?php

namespace app\repositories;

interface EntityRepositoryInterface {
    
    public function findByAttribute(string $attribute, string $value);

    public function save($model): bool;
    
}
