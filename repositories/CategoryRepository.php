<?php

namespace app\repositories;
use app\models\Category;

class CategoryRepository implements EntityRepositoryInterface {
    
    public function findByAttribute(string $attribute, string $value): ?Category {
        if(in_array($attribute, Category::instance()->attributes())) {
            return Category::find()->andWhere([$attribute => $value])->one();
        } else {
            throw new \yii\db\Exception('Attribute "'.$attribute.'" is not a valid attribute');
        }
    }
    
    public function save($category) :bool {
        return $category->save();
    }
}