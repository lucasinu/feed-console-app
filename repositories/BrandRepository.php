<?php

namespace app\repositories;
use app\models\Brand;

class BrandRepository implements EntityRepositoryInterface {
    
    public function findByAttribute(string $attribute, string $value): ?Brand {
        if(in_array($attribute, Brand::instance()->attributes())) {
            return Brand::find()->andWhere([$attribute => $value])->one();
        } else {
            throw new \yii\db\Exception('Attribute "'.$attribute.'" is not a valid attribute');
        }
    }

    public function save($brand): bool {
        return $brand->save();
    }
    
}