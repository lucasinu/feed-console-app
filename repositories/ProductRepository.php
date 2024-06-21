<?php

namespace app\repositories;
use app\models\Product;

class ProductRepository implements EntityRepositoryInterface {
    
    public function findByAttribute(string $attribute, string $value): ?Product {
        if(in_array($attribute, Product::instance()->attributes())) {
            return Product::find()->andWhere([$attribute => $value])->one();
        } else {
            throw new \yii\db\Exception('Attribute "'.$attribute.'" is not a valid attribute');
        }
    }

    public function save($product) : bool {
        return $product->save();
    }

}