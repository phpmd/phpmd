<?php

namespace PHPMD\Test\Anonymous;

use PHPMD\Attribute\SuppressWarnings;
use PHPMD\Rule\Naming\LongVariable;
use PHPMD\Rule\UnusedSuppression;

class AnonymousClasses
{
    public function methodSuppressions(): object
    {
        return new class {
            /**
             * @SuppressWarnings(PHPMD.LongVariable)
             * @SuppressWarnings(PHPMD.UnusedPrivateField)
             */
            private int $suppressedLongPrivateField = 1;

            /**
             * @SuppressWarnings(PHPMD.LongVariable)
             */
            public function suppressedByDocblock(): int
            {
                $docblockVariableNameIsFarTooLong = 1;

                return $docblockVariableNameIsFarTooLong;
            }

            #[SuppressWarnings(LongVariable::class)]
            public function suppressedByAttribute(): int
            {
                $attributeVariableNameIsFarTooLong = 2;

                return $attributeVariableNameIsFarTooLong;
            }

            #[SuppressWarnings]
            public function suppressedByWildcardAttribute(): int
            {
                $wildcardVariableNameIsFarTooLong = 3;

                return $wildcardVariableNameIsFarTooLong;
            }

            public function unsuppressedSibling(): int
            {
                $unsuppressedVariableNameIsFarTooLong = 4;

                return $unsuppressedVariableNameIsFarTooLong;
            }

            #[SuppressWarnings(LongVariable::class)]
            public function unusedAttribute(): int
            {
                return 5;
            }

            #[SuppressWarnings(LongVariable::class)]
            #[SuppressWarnings(UnusedSuppression::class)]
            public function suppressedUnusedAttribute(): int
            {
                return 6;
            }

            public function nestedAnonymousClass(): object
            {
                return new class {
                    #[SuppressWarnings(LongVariable::class)]
                    public function innerSuppressed(): int
                    {
                        $innerVariableNameIsFarTooLong = 7;

                        return $innerVariableNameIsFarTooLong;
                    }

                    #[SuppressWarnings]
                    public function innerUnused(): int
                    {
                        return 8;
                    }
                };
            }
        };
    }

    public function classStatementSuppressions(): object
    {
        /**
         * @SuppressWarnings(PHPMD.LongVariable)
         */
        $assigned = new class {
            public function insideAssigned(): int
            {
                $assignedVariableNameIsFarTooLong = 9;

                return $assignedVariableNameIsFarTooLong;
            }
        };

        $afterAssignedNameIsFarTooLong = $assigned->insideAssigned();

        // @SuppressWarnings(PHPMD.LongVariable)
        $nonDocComment = new class {
            public function insideNonDoc(): int
            {
                $nonDocVariableNameIsFarTooLong = 10;

                return $nonDocVariableNameIsFarTooLong;
            }
        };

        /**
         * @SuppressWarnings(PHPMD.LongVariable)
         */
        return new class ($afterAssignedNameIsFarTooLong, $nonDocComment) {
            public function __construct(
                private int $firstValueNameIsFarTooLong,
                private object $secondValue,
            ) {
            }

            public function read(): int
            {
                return $this->firstValueNameIsFarTooLong + $this->secondValue->insideNonDoc();
            }
        };
    }

    public function containsNestedFunction(): void
    {
        function nestedFunctionWithAnonymousClass(): object
        {
            return new class {
                #[SuppressWarnings(LongVariable::class)]
                public function unusedInNestedFunction(): int
                {
                    return 11;
                }
            };
        }
    }
}

function standaloneFunctionWithAnonymousClass(): object
{
    return new class {
        /**
         * @SuppressWarnings(PHPMD.LongVariable)
         */
        public int $suppressedFieldInFunction = 12;

        #[SuppressWarnings(LongVariable::class)]
        public function suppressedMethodInFunction(): int
        {
            $functionMethodNameIsFarTooLong = 13;

            return $functionMethodNameIsFarTooLong;
        }
    };
}
