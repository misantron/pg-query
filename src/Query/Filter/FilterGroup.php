<?php

namespace Misantron\QueryBuilder\Query\Filter;

use Misantron\QueryBuilder\Stringable;

/**
 * Class FilterGroup.
 */
class FilterGroup implements Stringable
{
    /**
     * @var Filter[]
     */
    private $list = [];

    /**
     * @return bool
     */
    public function notEmpty(): bool
    {
        return !empty($this->list);
    }

    /**
     * @param Filter $filter
     */
    public function append(Filter $filter)
    {
        $this->list[] = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        $trimSign = true;
        $filters = $this->list;

        return (string)array_reduce(
            $filters,
            function (string $query, Filter $filter) use (&$trimSign) {
                $condition = $filter->condition();
                if (!$trimSign && $filter->conjunction()) {
                    $condition = $filter->conjunction() . ' ' . $condition;
                }
                $trimSign = false;
                if ($filter->group()) {
                    $trimSign = true;
                }

                return trim($query . ' ' . $condition);
            },
            ''
        );
    }
}
