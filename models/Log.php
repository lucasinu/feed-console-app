<?php

namespace app\models;

use Yii;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * This is the model class for table "log".
 *
 * @property int $idlog
 * @property string|null $level
 * @property string|null $category
 * @property string|null $log_time
 * @property string|null $note
 * @property string|null $action_name
 * @property string|null $instance
 * @property string|null $id_instance
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['log_time'], 'safe'],
            [['note'], 'string'],
            [['level', 'category', 'action_name', 'instance', 'id_instance'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idlog' => 'Idlog',
            'level' => 'Livello',
            'category' => 'Categoria',
            'log_time' => 'Data',
            'note' => 'Note',
            'action_name' => 'Azione',
            'instance' => 'Tipo Oggetto',
            'id_instance' => 'Oggetto',
        ];
    }
    
    public function beforeSave($insert) {
        parent::beforeSave($insert);   
        if ($this->log_time!=null){
            $this->log_time = Yii::$app->formatter->asDate($this->log_time.' '.Yii::$app->getTimeZone(), Yii::$app->params['db_datetime']);
        }       
        return true;
    }
    
    public function afterSave($insert, $changedAttributes) {     
        if ($this->log_time!=null){
            $this->log_time = Yii::$app->formatter->asDate($this->log_time.' '.Yii::$app->getTimeZone(), Yii::$app->params['app_datetime']);
        }      
        parent::afterSave($insert, $changedAttributes);
    }
    
    public function afterFind() {
        parent::afterFind();     
        if ($this->log_time!=null){
            $this->log_time = Yii::$app->formatter->asDate($this->log_time.' '.Yii::$app->getTimeZone(), Yii::$app->params['app_datetime']);
        }   
    }
    
    public static function info($attr){
        $attr['category']='info';
        $attr['level']=1;
        self::setValue($attr);
    }
    
    public static function warning($attr){
        $attr['category']='warning';
        $attr['level']=2;
        self::setValue($attr);
    }
    public static function error($attr){
        $attr['category']='error';
        $attr['level']=3;
        self::setValue($attr);
    }

    public static function setValue($attr){
        
        $date = new \DateTime("now", new \DateTimeZone('Europe/Berlin'));

        $model = new Log();
        $model->level = $attr['level'] ?? null;
        $model->category = $attr['category'] ?? null;
        $model->log_time = $date->format(Yii::$app->params['default_datetime']);
        $model->action_name = $attr['action_name'] ?? null;
        $model->instance = $attr['instance'] ?? null;
        $model->id_instance = $attr['id_instance'] ?? null;
        $model->note = $attr['note'] ?? null;
        $model->save(false);
    }
    
    
    public static function writeExcel(array $log_data = []) {
                
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
