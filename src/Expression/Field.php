<?php

declare(strict_types=1);

namespace Misantron\QueryBuilder\Expression;

use Misantron\QueryBuilder\Compilable;
use Misantron\QueryBuilder\Helper\Escape;

/**
 * Class Field.
 */
final class Field implements Compilable
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
     *
     * @return Field
     */
    public static function create(string $expression, string $alias = ''): Field
    {
        return new static($expression, $alias);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): string
    {
        return $this->expression . ($this->alias ? ' AS ' . $this->alias : '');
    }
}
