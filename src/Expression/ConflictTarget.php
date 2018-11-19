<?php

namespace Misantron\QueryBuilder\Expression;

use Misantron\QueryBuilder\Query\Filter\FilterGroup;
use Misantron\QueryBuilder\Stringable;

/**
 * Class ConflictTarget.
 */
class ConflictTarget implements Stringable
{
    /**
     * @var string
     */
    private $expr;

    /**
     * Instance can be created from static factory method only.
     *
     * @param string $expr
     */
    private function __construct(string $expr)
    {
        $this->expr = $expr;
    }

    /**
     * Create target from table field name.
     *
     * @param string $field
     *
     * @return ConflictTarget
     */
    public static function fromField(string $field): ConflictTarget
    {
        return new static('(' . $field . ')');
    }

    /**
     * Create target from table constraint name.
     *
     * @param string $constraint
     *
     * @return ConflictTarget
     */
    public static function fromConstraint(string $constraint): ConflictTarget
    {
        return new static('ON CONSTRAINT ' . $constraint);
    }

    /**
     * Create target from where clause with a predicate.
     *
     * @param FilterGroup $filterGroup
     *
     * @return ConflictTarget
     */
    public static function fromCondition(FilterGroup $filterGroup): ConflictTarget
    {
        if ($filterGroup->isEmpty()) {
            throw new \InvalidArgumentException('Condition is empty');
        }

        return new static('WHERE ' . (string)$filterGroup);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->expr ?: '';
    }
}
