<?php

namespace PhpArsenal\DocumentValueUpdater;

use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyAccessor
{
    private \Symfony\Component\PropertyAccess\PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getValue(object $document, string $fieldName)
    {
        $getMethodName = $this->getGetterName($document, $fieldName);

        if (method_exists($document, $getMethodName)) {
            return $document->$getMethodName();
        }

        return $this->propertyAccessor->getValue($document, $fieldName);
    }

    public function setValue(object $document, string $fieldName, $value): void
    {
        $setMethodName = $this->getSetterName($document, $fieldName);

        if (method_exists($document, $setMethodName)) {
            $document->$setMethodName($value);
        }

        $this->propertyAccessor->setValue($document, $fieldName, $value);
    }

    public function isReadable(object $document, string $fieldName): bool
    {
        if ($this->getGetterName($document, $fieldName)) {
            return true;
        }

        return $this->propertyAccessor->isReadable($document, $fieldName);
    }

    public function isWritable(object $document, string $fieldName): bool
    {
        if ($this->getSetterName($document, $fieldName)) {
            return true;
        }

        return $this->propertyAccessor->isWritable($document, $fieldName);
    }

    public function getGetterName(object $document, string $fieldName): ?string
    {
        $getMethodName = sprintf('get%s', ucfirst($fieldName));
        if (method_exists($document, $getMethodName)) {
            return $getMethodName;
        }

        $getMethodName = sprintf('is%s', ucfirst($fieldName));
        if (method_exists($document, $getMethodName)) {
            return $getMethodName;
        }

        return null;
    }

    public function getSetterName(object $document, string $fieldName): ?string
    {
        $setMethodName = sprintf('set%s', ucfirst($fieldName));
        if (method_exists($document, $setMethodName)) {
            return $setMethodName;
        }

        return null;
    }
}