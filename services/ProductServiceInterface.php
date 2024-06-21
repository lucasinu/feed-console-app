<?php

namespace app\services;

use app\models\Product;
use SimpleXMLElement;

interface ProductServiceInterface
{
    public function processProduct(SimpleXMLElement $node): Product;
}
