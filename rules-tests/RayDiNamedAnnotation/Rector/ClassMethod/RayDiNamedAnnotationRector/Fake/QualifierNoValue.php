<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

class QualifierNoValue
{
    /**
     * @Bar
     */
    public function __construct(int $a, int $b)
    {
    }
}
