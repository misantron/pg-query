<?php

namespace Misantron\QueryBuilder\Expression;

use Misantron\QueryBuilder\Helper\Escape;
use Misantron\QueryBuilder\Stringable;

/**
 * Class Field
 * @package MediaTech\Query\Expression
 */
class Field implements Stringable
{
    use Escape;

    /**
     * @var string
     */
    private $expression;

    /**
     * @var string
     */
    private $alias;

    /**
     * @param string $expression
     * @param string $alias
     */
    private function __construct(string $expression, string $alias)
    {
        $this->expression = $expression;
        $this->alias = $this->escapeIdentifier($alias);
    }

    /**
     * @param string $expression
     * @param string $alias
     * @return Field
     */
    public static function create(string $expression, string $alias = ''): Field
    {
        return new static($expression, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return $this->expression . ($this->alias ? ' AS ' . $this->alias : '');
    }
}