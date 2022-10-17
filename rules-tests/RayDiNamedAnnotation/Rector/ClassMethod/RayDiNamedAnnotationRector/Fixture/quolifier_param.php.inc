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
?>
-----
<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

class QualifierParam
{
    public function __construct(#[\Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture\Foo]
    int $a, int $b)
    {
    }
}
?>
