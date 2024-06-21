<?php 

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\services\ProductService;
use app\services\CategoryService;
use app\services\BrandService;
use app\services\LogService;
use SimpleXMLElement;
use XMLReader;

class ImportXmlController extends Controller
{
    private $productService;
    private $categoryService;
    private $brandService;
    private $logService;
    private $items = 0;
    private $new_items = 0;
    private $log_data = [];

    public function __construct(
        $id, 
        $module, 
        ProductService $productService, 
        CategoryService $categoryService, 
        BrandService $brandService, 
        LogService $logService,
        $config = []
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->brandService = $brandService;
        $this->logService = $logService;
        parent::__construct($id, $module, $config);
    }

    private function parseNode(SimpleXMLElement $node)
    {

        $product = $this->productService->processProduct($node);
                
        if ((string)$node->Brand != '') {
            $brand = $this->brandService->findOrCreate((string)$node->Brand);
            if($brand->idbrand) {
                $product->brand_idbrand = $brand->idbrand;
            } else {
                print_r('Brand with name "'.$brand->name.'" not saved! See the log file for further information.');
                $this->log_data[] = ['import', 'brand', $brand->name, json_encode($brand->errors)];
            }
        }

        if ((string)$node->CategoryName != '') {
            $category = $this->categoryService->findOrCreate((string)$node->CategoryName);
            if($category->idcategory) {
                $product->category_idcategory = $category->idcategory;
            } else {
                print_r('Category with name "'.$category->name.'" not saved! See the log file for further information.');
                $this->log_data[] = ['import', 'category', $category->name, json_encode($category->errors)];
            }
        }
        
        if (!$product->save()) { 
            print_r('Product with external_id "'.$product->external_id.'" not saved! See the log file for further information.');
            $this->log_data[] = ['import', 'product', $product->external_id, json_encode($product->errors)];
        } else {
            $this->log_data[] = ['import', 'product', $product->external_id, 'Product added!'];
            $this->new_items++;
        }
    }

    public function actionImport($file = null)
    {
        if ($file == null) {
            $file = \Yii::$app->params['default_source_file'];
        }

        if (file_exists($file)) {
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

            $end_time = microtime(true);
            $exec_time = $end_time - $star_time;

            $this->logService->writeExcel($this->log_data);
            print_r("XML Data imported in " . number_format($exec_time, 2) . " seconds.\nProcessed items: " . $this->items . "\nNew items: " . $this->new_items);
            return ExitCode::OK;
        } else {
            $this->log_data[] = ['import', 'filename', $file, 'File: "' . $file . '" does not exist!'];
            $this->logService->writeExcel($this->log_data);
            print_r("XML file does not exist!");
            return ExitCode::IOERR;
        }
    }
}
