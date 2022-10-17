<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

class QualifierParam
{
    /**
     * @Foo("a")
     */
    public function __construct(int $a, int $b)
    {
    }
}
