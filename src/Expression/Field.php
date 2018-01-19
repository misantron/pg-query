<?php

namespace MediaTech\Query\Expression;


use MediaTech\Query\Escape;
use MediaTech\Query\Renderable;

/**
 * Class Field
 * @package MediaTech\Query\Expression
 */
class Field implements Renderable
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
        $this->alias = $this->escapeIdentifier($alias, false);
    }

    /**
     * @param string $expression
     * @param string|null $alias
     * @return Field
     */
    public static function create(string $expression, $alias = null)
    {
        return new static($expression, $alias ?? '');
    }

    /**
     * @return string
     */
    public function build(): string
    {
        return $this->expression . ($this->alias ? ' AS ' . $this->alias : '');
    }
}