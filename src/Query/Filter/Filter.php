<?php

namespace MediaTech\Query\Query\Filter;


use MediaTech\Query\Stringable;

/**
 * Class Filter
 * @package MediaTech\Query\Query\Mixin\Filter
 */
class Filter
{
    const CONJUNCTIONS = ['AND', 'OR', ''];

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
     * @param bool $group
     */
    private function __construct(string $condition, string $conjunction, bool $group)
    {
        if (!in_array($conjunction, self::CONJUNCTIONS)) {
            throw new \InvalidArgumentException('Invalid condition conjunction: unexpected value');
        }

        $this->condition = $condition;
        $this->conjunction = $conjunction;
        $this->group = $group;
    }

    /**
     * @param Stringable|string $condition
     * @param string $conjunction
     * @param bool $group
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