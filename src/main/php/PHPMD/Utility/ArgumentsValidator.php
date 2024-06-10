<?php

namespace PHPMD\Utility;

use InvalidArgumentException;

final class ArgumentsValidator
{
    /**
     * @param string[] $originalArguments
     * @param string[] $arguments
     */
    public function __construct(
        private readonly bool $hasImplicitArguments,
        private readonly array $originalArguments,
        private readonly array $arguments,
    ) {
    }

    /**
     * Throw an exception if the given $value cannot be used as a value for the argument $name.
     *
     * @throws InvalidArgumentException if the given $value cannot be used as a value for the argument $name
     */
    public function validate(string $name, string $value): void
    {
        if (!$this->hasImplicitArguments) {
            return;
        }

        if (!str_starts_with($value, '-')) {
            return;
        }

        $options = array_diff($this->originalArguments, $this->arguments, ['--']);

        throw new InvalidArgumentException(
            'Unknown option ' . $value . '.' . PHP_EOL .
            'If you intend to use "' . $value . '" as a value for ' . $name . ' argument, ' .
            'use the explicit argument separator:' . PHP_EOL .
            rtrim('phpmd ' . implode(' ', $options)) . ' -- ' .
            implode(' ', $this->arguments)
        );
    }
}
