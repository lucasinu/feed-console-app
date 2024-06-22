<?php

namespace app\services;

use app\models\Product;
use SimpleXMLElement;

interface ProductServiceInterface
{
    public function processProduct(SimpleXMLElement $node): Product;
    
    public function processProductNode(SimpleXMLElement $node, &$log_data, &$new_items): Product;

}
