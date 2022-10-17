<?php

declare(strict_types=1);

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod;

use Iterator;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;
use Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fake\Foo;
use Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fake\NoneNamed;
use Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fake\SomeClass;

use function class_exists;

final class AnnotationBindingRectorTest extends AbstractRectorTestCase
{
    /** @dataProvider provideData() */
    public function test(string $filePath): void
    {
        $a = class_exists(Foo::class);
        $b = class_exists(NoneNamed::class);
        $c = class_exists(SomeClass::class);
        $this->doTestFile($filePath);
    }

    /** @return Iterator<<string>> */
    public function provideData(): Iterator
    {
        return $this->yieldFilesFromDirectory(__DIR__ . '/Fixture');
    }

    public function provideConfigFilePath(): string
    {
        return __DIR__ . '/config/configured_rule.php';
    }
}
