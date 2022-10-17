<?php

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture;

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

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture;

class QualifierNoValue
{
    public function __construct(#[\Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture\Bar]
    int $a, #[\Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture\Bar]
    int $b)
    {
    }
}
?>
