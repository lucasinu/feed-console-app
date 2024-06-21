<?php

namespace app\services;

use Yii;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LogService {
    
    public function writeExcel(array $log_data = []) {
                
        if(!empty($log_data)) {
        
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->getActiveSheet();
            
            $header = ['Action', 'Instance type', 'Instance', 'Description'];
            $worksheet->fromArray($header, null, 'A1');

            foreach ($log_data as $rowNum => $rowData) {
                $worksheet->fromArray($rowData, null, 'A' . ($rowNum + 2));
            }

            $writer = new Xlsx($spreadsheet);
            $file_name = 'output.xlsx';
            $path = Yii::$app->params['log_path'].$file_name;
            // Output file uniqueness without overwriting existing files
            if (file_exists($path)) {
                $i = 0;
                // Extract filename and extension using pathinfo()
                $pathInfo = pathinfo($file_name);
                $name = $pathInfo['filename']; // Extract filename
                $ext = isset($pathInfo['extension']) ? $pathInfo['extension'] : ''; // Extract extension (if exists)

                // Loop to find a unique filename
                while (file_exists($path)) {
                    $i++;
                    $path = Yii::$app->params['log_path'] . $name . '_' . $i . ($ext ? '.' . $ext : '');
                }
            }

            $writer->save($path);
        }
    }
}
