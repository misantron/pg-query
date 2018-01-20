<?php

namespace MediaTech\Query\Query\Mixin\Filter;


use MediaTech\Query\Renderable;

/**
 * Class FilterGroup
 * @package MediaTech\Query\Query\Mixin\Filter
 */
class FilterGroup implements Renderable
{
    /**
     * @var array
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
     * @return string
     */
    public function build(): string
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