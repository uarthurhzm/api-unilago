<?php

namespace App\Shared\DTO;

use ReflectionClass;
use ReflectionProperty;

abstract class BaseDTO
{
    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    private function hydrate(array $data): void
    {
        $reflection = new ReflectionClass($this);
        
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            
            if (array_key_exists($propertyName, $data)) {
                $type = $property->getType();
                
                if ($type && !$type->isBuiltin()) {
                    $className = $type->getName();
                    $this->$propertyName = new $className($data[$propertyName]);
                } else {
                    $this->$propertyName = $data[$propertyName];
                }
            }
        }
    }

    public function validate(): array
    {
        $errors = [];
        $reflection = new ReflectionClass($this);
        
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $type = $property->getType();
            
            if ($type && !$type->allowsNull() && !isset($this->$propertyName)) {
                $errors[] = "O campo '{$propertyName}' é obrigatório";
            }
        }
        
        return $errors;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}