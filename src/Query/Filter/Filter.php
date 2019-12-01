<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Filter;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Query\Condition\Condition;

/**
 * Class Filter.
 */
final class Filter
{
    /**
     * @var string
     */
    private $condition;

    /**
     * @var string
     */
    private $conjunction;

    /**
     * @var bool
     */
    private $group;

    private function __construct(string $condition, string $conjunction, bool $group)
    {
        QueryAssert::validConjunctionOperator($conjunction);

        $this->condition = $condition;
        $this->conjunction = $conjunction;
        $this->group = $group;
    }

    /**
     * @param Condition|string $condition
     */
    public static function create($condition, string $conjunction = '', bool $group = false): Filter
    {
        if ($condition instanceof Condition) {
            $condition = $condition->compile();
        }

        return new static($condition, $conjunction, $group);
    }

    public function condition(): string
    {
        return $this->condition;
    }

    public function conjunction(): string
    {
        return $this->conjunction;
    }

    public function group(): bool
    {
        return $this->group;
    }
}
