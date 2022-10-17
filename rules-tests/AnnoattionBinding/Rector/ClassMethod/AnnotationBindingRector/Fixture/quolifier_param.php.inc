<?php

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture;

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

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture;

class QualifierParam
{
    public function __construct(#[\Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture\Foo]
    int $a, int $b)
    {
    }
}
?>
