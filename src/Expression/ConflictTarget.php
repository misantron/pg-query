<?php

namespace Misantron\QueryBuilder\Expression;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Compilable;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;

/**
 * Class ConflictTarget.
 */
class ConflictTarget implements Compilable
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
     * @param FilterGroup $group
     *
     * @return ConflictTarget
     */
    public static function fromCondition(FilterGroup $group): ConflictTarget
    {
        QueryAssert::filterGroupNotEmpty($group);

        return new static('WHERE ' . $group);
    }

    /**
     * @return string
     */
    public function compile(): string
    {
        return $this->expr;
    }
}
