<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Expression;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Compilable;
use Misantron\QueryBuilder\Query\Filter\FilterGroup;

/**
 * Class ConflictTarget.
 */
final class ConflictTarget implements Compilable
{
    /**
     * @var string
     */
    private $expr;

    /**
     * Instance can be created from static factory method only.
     */
    private function __construct(string $expr)
    {
        $this->expr = $expr;
    }

    /**
     * Create target from table field name.
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
     * @return ConflictTarget
     */
    public static function fromConstraint(string $constraint): ConflictTarget
    {
        return new static('ON CONSTRAINT ' . $constraint);
    }

    /**
     * Create target from where clause with a predicate.
     *
     * @return ConflictTarget
     */
    public static function fromCondition(FilterGroup $group): ConflictTarget
    {
        QueryAssert::filterGroupNotEmpty($group);

        return new static('WHERE ' . $group->compile());
    }

    /**
     * @return string
     */
    public function compile(): string
    {
        return $this->expr;
    }
}
