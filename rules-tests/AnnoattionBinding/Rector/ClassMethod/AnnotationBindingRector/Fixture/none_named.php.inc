<?php

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture;

class NoneNamed
{
    /**
     * @Foo
     */
    public function __construct(int $a, int $b)
    {
    }
}

?>
-----
<?php

namespace Rector\Tests\AnnoattionBinding\Rector\ClassMethod\AnnotationBindingRector\Fixture;

class NoneNamed
{
    /**
     * @Foo
     */
    public function __construct(int $a, int $b)
    {
    }
}

?>
