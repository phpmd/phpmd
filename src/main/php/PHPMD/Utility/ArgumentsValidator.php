<?php

namespace PHPMD\Utility;

use InvalidArgumentException;
use RuntimeException;

class ArgumentsValidator
{
    /** @var bool */
    private $hasImplicitArguments;
    /** @var string[] */
    private $originalArguments;
    /** @var string[] */
    private $arguments;

    public function __construct($hasImplicitArguments, $originalArguments, $arguments)
    {
        $this->hasImplicitArguments = $hasImplicitArguments;
        $this->originalArguments = $originalArguments;
        $this->arguments = $arguments;
    }

    public function validate($name, $value)
    {
        if (!$this->hasImplicitArguments) {
            return;
        }

        if (substr($value, 0, 1) !== '-') {
            return;
        }

        $options = array_diff($this->originalArguments, $this->arguments, array('--'));

        throw new InvalidArgumentException(
            'Unknown option ' . $value . '.' . PHP_EOL .
            'If you intend to use "' . $value . '" as a value for ' . $name . ' argument, ' .
            'use the explicit argument separator:' . PHP_EOL .
            rtrim('phpmd ' . implode(' ', $options)) . ' -- ' .
            implode(' ', $this->arguments)
        );
    }
}
