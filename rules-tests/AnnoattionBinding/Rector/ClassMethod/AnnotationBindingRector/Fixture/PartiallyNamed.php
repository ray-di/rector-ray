<?php

declare(strict_types=1);

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture;

use Ray\Di\Di\Named;

class PartiallyNamed
{
    /**
     * @Named("a=foo,b=bar")
     * @Foo
     */
    public function __construct(int $a, int $b, $notNamed)
    {
    }
}
