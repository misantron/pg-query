<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Query\Filter;

use Misantron\QueryBuilder\Compilable;

/**
 * Class FilterGroup.
 */
final class FilterGroup implements Compilable
{
    /**
     * @var Filter[]
     */
    private $list = [];

    public function notEmpty(): bool
    {
        return !empty($this->list);
    }

    public function isEmpty(): bool
    {
        return empty($this->list);
    }

    public function append(Filter $filter): void
    {
        $this->list[] = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        $trimSign = true;
        $filters = $this->list;

        return (string) array_reduce(
            $filters,
            static function (string $query, Filter $filter) use (&$trimSign) {
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
