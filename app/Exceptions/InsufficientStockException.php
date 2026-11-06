<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $variantName, float $requested, float $available)
    {
        parent::__construct(
            "Stock insuficiente para \"{$variantName}\": se pidió {$requested}, disponible {$available}."
        );
    }
}
