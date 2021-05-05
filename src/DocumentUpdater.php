<?php

namespace PhpArsenal\DocumentValueUpdater;

use ReflectionClass;

class DocumentUpdater
{
    private PropertyAccessor $propertyAccessor;

    private array $ignoredProperties = ['id'];

    public function __construct(PropertyAccessor $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function updateWithValues(object $document, array $newValues): void
    {
        foreach ($newValues as $propertyName => $newValue) {
            $isIgnoredProperty = in_array(strtolower($propertyName), $this->ignoredProperties);

            if (!$isIgnoredProperty && $this->propertyAccessor->isWritable($document, $propertyName)) {
                $this->propertyAccessor->setValue($document, $propertyName, $newValue);
            }
        }
    }

    public function updateWithDocument(object $document, object $newDocument, ?array $allowedProperties = [], ?array $ignoredProperties = []): void
    {
        $classReflection = new ReflectionClass($newDocument);
        $allowedProperties = $allowedProperties ?: [];
        $ignoredProperties = $ignoredProperties ?: [];
        $checkAllowedProperties = count($allowedProperties) > 0;
        $checkIgnoredProperties = count($ignoredProperties) > 0;

        foreach ($classReflection->getProperties() as $propertyReflection) {
            $propertyName = $propertyReflection->getName();

            if ($checkAllowedProperties && !in_array($propertyName, $allowedProperties)) {
                continue;
            }

            if ($checkIgnoredProperties && in_array($propertyName, $ignoredProperties)) {
                continue;
            }

            if (in_array(strtolower($propertyName), $this->ignoredProperties)) {
                continue;
            }

            if ($this->propertyAccessor->isReadable($newDocument, $propertyName)) {
                $newValue = $this->propertyAccessor->getValue($newDocument, $propertyName);

                if ($newValue !== null && $this->propertyAccessor->isWritable($document, $propertyName)) {
                    $this->propertyAccessor->setValue($document, $propertyName, $newValue);
                }
            }
        }
    }
}