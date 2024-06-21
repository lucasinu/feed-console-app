<?php

namespace app\services;

interface EntityServiceInterface
{
    public function findOrCreate(string $name);
}
