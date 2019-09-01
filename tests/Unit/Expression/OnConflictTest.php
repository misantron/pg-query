<?php
declare(strict_types=1);

namespace Misantron\QueryBuilder\Tests\Unit\Expression;

use Misantron\QueryBuilder\Expression\ActionNothing;
use Misantron\QueryBuilder\Expression\ConflictTarget;
use Misantron\QueryBuilder\Expression\OnConflict;
use Misantron\QueryBuilder\Factory;
use Misantron\QueryBuilder\Query\Update;
use Misantron\QueryBuilder\Tests\Unit\UnitTestCase;

class OnConflictTest extends UnitTestCase
{
    public function testConstructor(): void
    {
        $conflict = $this->createOnConflictWithoutAction();

        $this->assertPropertyInstanceOf(ConflictTarget::class, 'target', $conflict);
        $this->assertPropertyInstanceOf(ActionNothing::class, 'action', $conflict);

        $conflict = $this->createOnConflictWithAction();

        $this->assertPropertyInstanceOf(ConflictTarget::class, 'target', $conflict);
        $this->assertPropertyInstanceOf(Update::class, 'action', $conflict);
    }

    public function testCompile(): void
    {
        $conflict = $this->createOnConflictWithoutAction();

        $this->assertSame(' ON CONFLICT (test) DO NOTHING', $conflict->compile());

        $conflict = $this->createOnConflictWithAction();

        $this->assertSame(" ON CONFLICT (test) DO UPDATE SET foo = 'bar' WHERE baz = 5", $conflict->compile());
    }

    private function createOnConflictWithoutAction(): OnConflict
    {
        $target = ConflictTarget::fromField('test');
        return new OnConflict($target);
    }

    private function createOnConflictWithAction(): OnConflict
    {
        $target = ConflictTarget::fromField('test');
        $factory = Factory::create($this->createServerMock());

        /** @var Update $action */
        $action = $factory
            ->update()
            ->set(['foo' => 'bar'])
            ->andEquals('baz', 5);

        return new OnConflict($target, $action);
    }
}
