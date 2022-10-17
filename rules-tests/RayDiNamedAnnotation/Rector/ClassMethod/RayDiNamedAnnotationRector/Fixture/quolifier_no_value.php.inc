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
?>
-----
<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

class QualifierNoValue
{
    public function __construct(#[\Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture\Bar]
    int $a, #[\Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture\Bar]
    int $b)
    {
    }
}
?>
