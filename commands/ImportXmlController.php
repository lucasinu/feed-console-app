<?php 

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\services\ProductService;
use app\services\LogService;
use XMLReader;

class ImportXmlController extends Controller
{
    private $productService;
    private $logService;
    private $items = 0;
    private $new_items = 0;
    private $log_data = [];

    public function __construct(
        $id, 
        $module, 
        ProductService $productService,  
        LogService $logService,
        $config = []
    ) {
        $this->productService = $productService;
        $this->logService = $logService;
        parent::__construct($id, $module, $config);
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
            
            // Skip the root element (<catalog>)
            $reader->read();

            // To advance the XMLReader to the first item
            while ($reader->read() && $reader->name !== 'item');
            
            while ($reader->name === 'item') {
                $node = new \SimpleXMLElement($reader->readOuterXml());
                $this->productService->processProductNode($node, $this->log_data, $this->new_items);
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
            $this->log_data[] = ['import', 'error', 'filename', $file, 'File: "' . $file . '" does not exist!'];
            $this->logService->writeExcel($this->log_data);
            print_r("XML file does not exist!");
            return ExitCode::IOERR;
        }
    }
}
