<?php

declare(strict_types=1);

/**
 * This file is part of PHP Mess Detector.
 *
 * Copyright (c) Manuel Pichler <mapi@phpmd.org>.
 * All rights reserved.
 *
 * Licensed under BSD License
 * For full copyright and license information, please see the LICENSE file.
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Manuel Pichler <mapi@phpmd.org>
 * @copyright Manuel Pichler. All rights reserved.
 * @license https://opensource.org/licenses/bsd-license.php BSD License
 * @link http://phpmd.org/
 */

namespace PHPMD\RuleProperty;

use PHPMD\Exception\InvalidRulePropertyTypeException;
use PHPMD\Rule;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionProperty;
use ReflectionUnionType;

final class RulePropertySetter
{
    private static array $cache = [];

    private ?array $properties = null;

    private function __construct(
        /** @var class-string $class */
        private string $class,
    ) {
    }

    /** @param class-string $class */
    public static function forClass(string $class): self
    {
        return self::$cache[$class] ??= new self($class);
    }

    public static function setDefaultValues(Rule $rule): void
    {
        foreach (get_class_vars($rule::class) as $key => $value) {
            if (!isset($rule->$key)) {
                $parameters = self::forClass($rule::class)->getRulePropertyForKey($key);

                if ($parameters) {
                    [, $property, $ruleProperty] = $parameters;

                    if (!$property->hasDefaultValue()) {
                        $rule->$key = !$property->hasType() || $property->getType()->allowsNull()
                            ? null
                            : self::castValue(null, $property, $ruleProperty, $key);
                    }
                }
            }
        }
    }

    public static function setValue(Rule $rule, string $name, mixed $value): void
    {
        $parameters = self::forClass($rule::class)->getRulePropertyForKey($name);

        if ($parameters) {
            [$key, $property, $ruleProperty] = $parameters;
            $rule->$key = self::castValue($value, $property, $ruleProperty, $key);
        }
    }

    private static function castValue(
        mixed $value,
        ReflectionProperty $property,
        RuleProperty $ruleProperty,
        string $key,
    ): mixed {
        $type = $property->getType();
        $ruleClass = $property->getDeclaringClass()->getName();

        if (!$type || $type instanceof ReflectionUnionType || $type instanceof ReflectionIntersectionType) {
            throw new InvalidRulePropertyTypeException(
                $ruleClass,
                $key,
                'Use single type or explicitly mixed to allow any type',
            );
        }

        $typeName = $type->getName();

        return $type->isBuiltin()
            ? self::getBuiltInValue($typeName, $value)
            : self::getClassInstanceValue($typeName, $value, $ruleClass, $key, $ruleProperty);
    }

    private static function getBuiltInValue(string $type, mixed $value): mixed
    {
        return match ($type) {
            'bool' => $value === true || $value === 1 || $value === 1.0 ||
                (\is_string($value) && \in_array(strtolower($value), ['1', 'yes', 'on', 'true'], true)),
            'int' => (int)$value,
            'float' => (float)$value,
            'string' => (string)$value,
            'array' => (array)$value,
            'object' => (object)$value,
            default => $value,
        };
    }

    private static function getClassInstanceValue(
        string $class,
        mixed $value,
        string $ruleClass,
        string $key,
        RuleProperty $ruleProperty,
    ): object {
        if (!is_a($class, RulePropertyType::class, true)) {
            throw new InvalidRulePropertyTypeException(
                $ruleClass,
                $key,
                "$class does not implement " . RulePropertyType::class,
            );
        }

        $createFromRuleProperty = [$class, 'createFromRuleProperty'];

        if (!is_callable($createFromRuleProperty)) {
            $typeClass = new ReflectionClass($class);

            throw new InvalidRulePropertyTypeException(
                $class,
                $key,
                "class must be instantiable, $typeClass" . match (true) {
                    $typeClass->isTrait() => ' is a trait',
                    $typeClass->isInterface() => ' is an interface',
                    $typeClass->isAbstract() => ' is abstract',
                    default => '::createFromRuleProperty is not callable',
                },
            );
        }

        $value = $createFromRuleProperty(
            $ruleClass,
            $key,
            $value,
            $ruleProperty,
        );

        if (!is_a($value, $class)) {
            $valueType = \is_object($value) ? $value::class : \gettype($value);

            throw new InvalidRulePropertyTypeException(
                $ruleClass,
                $key,
                "$class instance expected, but $class::createFromRuleProperty() returned $valueType",
            );
        }

        return $value;
    }

    /** @return array{string, ReflectionProperty, RuleProperty}|null */
    private function getRulePropertyForKey(string $key): ?array
    {
        if (!isset($this->properties)) {
            $this->properties = [];

            foreach ($this->getReflectionClassProperties(new ReflectionClass($this->class)) as $property) {
                $name = $property->getName();

                $attributes = $property->getAttributes(RuleProperty::class, ReflectionAttribute::IS_INSTANCEOF);

                if ($attributes === []) {
                    continue;
                }

                if (count($attributes) > 1) {
                    throw new InvalidRulePropertyTypeException(
                        $property->getDeclaringClass()->getName(),
                        $name,
                        'Only 1 attribute per property can implement ' . RuleProperty::class,
                    );
                }

                /** @var RuleProperty $ruleProperty */
                $ruleProperty = $attributes[0]->newInstance();
                $parameters = [$name, $property, $ruleProperty];
                $dashedName = strtolower(preg_replace('/[A-Z]/', '-$0', $name));

                foreach ($ruleProperty->getKeys($dashedName) as $propertyName) {
                    $this->properties[$propertyName] = $parameters;
                }
            }
        }

        return $this->properties[$key] ?? null;
    }

    /** @return ReflectionProperty[] */
    private function getReflectionClassProperties(ReflectionClass $class): array
    {
        $properties = $class->getProperties();
        $parentClass = $class->getParentClass();

        if ($parentClass) {
            array_push($properties, ...$this->getReflectionClassProperties($parentClass));
        }

        foreach ($class->getTraits() as $trait) {
            array_push($properties, ...$this->getReflectionClassProperties($trait));
        }

        return $properties;
    }
}
