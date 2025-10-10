<?php

namespace App\Shared\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromBody
{
    public function __construct(
        public bool $validate = true
    ) {}
}