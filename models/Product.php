<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "product".
 *
 * @property int $idproduct
 * @property int $external_id
 * @property string|null $sku
 * @property string|null $name
 * @property float|null $price
 * @property string|null $link
 * @property string|null $image
 * @property float|null $rating
 * @property float|null $count
 * @property string|null $caffeine_type
 * @property int|null $flavored
 * @property int|null $seasonal
 * @property int|null $in_stock
 * @property int|null $facebook
 * @property int|null $is_k_cup
 * @property string|null $short_description
 * @property string|null $description
 * @property int $brand_idbrand
 * @property int $category_idcategory
 *
 * @property Brand $brandIdbrand
 * @property Category $categoryIdcategory
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['external_id'], 'required'],
            [['external_id', 'flavored', 'seasonal', 'in_stock', 'facebook', 'is_k_cup', 'brand_idbrand', 'category_idcategory'], 'integer'],
            [['price', 'rating', 'count'], 'number'],
            [['description', 'short_description'], 'string'],
            [['sku', 'caffeine_type'], 'string', 'max' => 45],
            [['name'], 'string', 'max' => 128],
            [['link', 'image'], 'string', 'max' => 255],
            [['brand_idbrand'], 'exist', 'skipOnError' => true, 'targetClass' => Brand::class, 'targetAttribute' => ['brand_idbrand' => 'idbrand']],
            [['category_idcategory'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_idcategory' => 'idcategory']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idproduct' => 'Idproduct',
            'external_id' => 'External ID',
            'sku' => 'Sku',
            'name' => 'Name',
            'price' => 'Price',
            'link' => 'Link',
            'image' => 'Image',
            'rating' => 'Rating',
            'count' => 'Count',
            'caffeine_type' => 'Caffeine Type',
            'flavored' => 'Flavored',
            'seasonal' => 'Seasonal',
            'in_stock' => 'In Stock',
            'facebook' => 'Facebook',
            'is_k_cup' => 'Is K Cup',
            'short_description' => 'Short Description',
            'description' => 'Description',
            'brand_idbrand' => 'Brand Idbrand',
            'category_idcategory' => 'Category Idcategory',
        ];
    }

    /**
     * Gets query for [[BrandIdbrand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBrandIdbrand()
    {
        return $this->hasOne(Brand::class, ['idbrand' => 'brand_idbrand'])->inverseOf('products');
    }

    /**
     * Gets query for [[CategoryIdcategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryIdcategory()
    {
        return $this->hasOne(Category::class, ['idcategory' => 'category_idcategory'])->inverseOf('products');
    }
}
