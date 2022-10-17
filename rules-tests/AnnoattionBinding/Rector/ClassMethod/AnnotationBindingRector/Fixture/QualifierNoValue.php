<?php

declare(strict_types=1);

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture;

class QualifierNoValue
{
    /** @Bar */
    public function __construct(int $a, int $b)
    {
    }
}
