<?php

namespace App\Shared\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class FromRoute
{
    public function __construct(
        public ?string $name = null
    ) {}
}