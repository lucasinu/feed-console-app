<?php

namespace app\commands;

use yii;
use yii\console\Controller;
use XMLReader;
use yii\console\ExitCode;
use app\models\Product;
use app\models\Log;
use app\models\Category;
use app\models\Brand;

class ImportXmlController extends Controller
{
    
    private $items = 0;
    private $new_items = 0;
    private $new_categories = 0;
    private $new_brands = 0;
    private $log_data = [];
    
    
    private function parseNode(\SimpleXMLElement $node) {
                 
        // Check if the product already exists to eventually update it and if not, create a new Product
        $product = Product::find()->andWhere(['external_id' => (int)$node->entity_id])->exists() 
                ? Product::find()->andWhere(['external_id' => (int)$node->entity_id])->one() 
                : new Product();
        
        $new = $product->isNewRecord ? true : false;
        
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
        
        if((string)($node->CategoryName) != '') {
            // Check if the Category already exists and if not, create a new Category and link it to the product
            $category = Category::find()->andWhere(['name' => (string)$node->CategoryName])->one();
            if(!isset($category->idcategory)) {
                $category = new Category();
                $category->name = (string)$node->CategoryName;
                if(!$category->save()) {
                    Log::error([
                        'action_name' => 'import',
                        'instance' => Category::tableName(),
                        'id_instance' => $category->name,
                        'note' => json_encode($category->errors)
                    ]);
                    // Add the error to log_data using the same structure of Log
                    $this->log_data[] = ['import' , Category::tableName(), $category->name, json_encode($category->errors)];
                } else {
                    $this->new_categories++;
                }
            }
            // If Category was not saved, then save without it
            $product->category_idcategory = $category->idcategory ?? null;
        } else {
            Log::error([
                'action_name' => 'import',
                'instance' => Product::tableName(),
                'id_instance' => 'Entity id: '.$product->external_id,
                'note' => 'Category name is empty'
            ]);
            // Add the error to log_data using the same structure of Log
            $this->log_data[] = ['import' , Product::tableName(), 'Entity id: '.$product->external_id, 'Category name is empty'];
        }
        
        if((string)($node->Brand) != '') {
            // Check if the Brand already exists and if not, create a new Brand and link it to the product
            $brand = Brand::find()->andWhere(['name' => (string)$node->Brand])->one();
            if(!isset($brand->idbrand)) {
                $brand = new Brand();
                $brand->name = (string)$node->Brand;
                if(!$brand->save()) {
                    Log::error([
                        'action_name' => 'import',
                        'instance' => Brand::tableName(),
                        'id_instance' => $brand->name,
                        'note' => json_encode($brand->errors)
                    ]);
                    // Add the error to log_data using the same structure of Log
                    $this->log_data[] = ['import' , Brand::tableName(), $brand->name, json_encode($brand->errors)];
                } else {
                    $this->new_brands++;
                }
            }
            // If Brand was not saved, then we save without it 
            $product->brand_idbrand = $brand->idbrand;
        } else {
            Log::error([
                'action_name' => 'import',
                'instance' => Product::tableName(),
                'id_instance' => 'Entity id: '.$product->external_id,
                'note' => 'Brand name is empty'
            ]);
            // Add the error to log_data using the same structure of Log
            $this->log_data[] = ['import' , Product::tableName(), 'Entity id: '.$product->external_id, 'Brand name is empty'];
        }
        
        if(!$product->save()) {
            Log::error([
                'action_name' => 'import',
                'instance' => Product::tableName(),
                'id_instance' => $product->external_id ,
                'note' => json_encode($product->errors)
            ]);
            // Add the error to log_data using the same structure of Log
            $this->log_data[] = ['import' , Product::tableName(), $product->external_id, json_encode($product->errors)];
            
        } else if($new) {
            $this->new_items++;
        }
    }
    
    public function actionImport($file = null ) {
        
        if($file == null) {
            $file = yii::$app->params['default_source_file'];
        }
        
        if(file_exists($file)) {
            // Start counting time
            $star_time = microtime(true);
            
            $reader = new XMLReader();
            $reader->open($file);
                    
            $reader->read(); // Skip the root element (<catalog>)
            
            // To advance the XMLReader to the first item
            while ($reader->read() && $reader->name !== 'item');
            
            while ($reader->name === 'item') {
                $node = new \SimpleXMLElement($reader->readOuterXml());
                $this->parseNode($node);
                $this->items++;
                $reader->next('item');
                
            }
            $reader->close();
            
            // End counting time
            $end_time = microtime(true);
            $exec_time = $end_time - $star_time;
            
            Log::writeExcel($this->log_data);
            
            echo "XML Data imported in " .number_format($exec_time, 2). " seconds.\n". PHP_EOL;
            echo "Processed items: " .$this->items. PHP_EOL;
            echo "New items: " .$this->new_items. PHP_EOL;
            echo "New categories: " .$this->new_categories. PHP_EOL;
            echo "New brands: " .$this->new_brands. PHP_EOL;
            return ExitCode::OK;
            
        } else {
            // Add the error to log_data using the same structure of Log
            $this->log_data[] = ['import' , 'filename', $file, 'File: "'.$file.'" does not exist!'];
            Log::writeExcel($this->log_data);
            echo "XML file does not exist!" . PHP_EOL;
            return ExitCode::IOERR;
            
        }

    }

}