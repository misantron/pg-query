<?php

namespace Misantron\QueryBuilder\Query\Filter;

use Misantron\QueryBuilder\Assert\QueryAssert;
use Misantron\QueryBuilder\Stringable;

/**
 * Class Filter.
 */
class Filter
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

    /**
     * @param string $condition
     * @param string $conjunction
     * @param bool   $group
     */
    private function __construct(string $condition, string $conjunction, bool $group)
    {
        QueryAssert::validConjunctionOperator($conjunction);

        $this->condition = $condition;
        $this->conjunction = $conjunction;
        $this->group = $group;
    }

    /**
     * @param Stringable|string $condition
     * @param string            $conjunction
     * @param bool              $group
     *
     * @return Filter
     */
    public static function create($condition, string $conjunction = '', bool $group = false)
    {
        return new static((string)$condition, $conjunction, $group);
    }

    /**
     * @return string
     */
    public function condition(): string
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function conjunction(): string
    {
        return $this->conjunction;
    }

    /**
     * @return bool
     */
    public function group(): bool
    {
        return $this->group;
    }
}
