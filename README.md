# Rector Rules for Ray.Di
[![Continuous Integration](https://github.com/ray-di/rector-ray/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/ray-di/rector-ray/actions/workflows/continuous-integration.yml)

The [rector/rector](http://github.com/rectorphp/rector) rules for Ray.Di.

## Install

```bash
composer require ray/rector-ray --dev
```

## Rules

### AnnotationBindingRector

This Rector converts annotation bindings in PHPDoc into parameter attribute bindings.

:wrench: **configure it!**

- class: [`AnnotationBindingRector`](rules/AnnotationBinding/Rector/ClassMethod/AnnotationBindingRector.php)

```php
use PHPStan\Type\ObjectType;
use Rector\Arguments\Rector\ClassMethod\ArgumentAdderRector;
use Rector\Arguments\ValueObject\ArgumentAdder;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->ruleWithConfiguration(
        AnnotationBindingRector::class
    );
};
```

↓

```diff
class SomeClass
{
    /**
-    * @Named("a=foo, b=bar")
     * @Foo
     */
-    public function __construct(int $a, int $b)
+    public function __construct(#[Named('foo')] int $a, #[Named('bar')] int $b)
    {
    }
```

```diff
-    /**
-     * @Foo("a")
-     */
-    public function __construct(int $a, int $b)
+    public function __construct(#[Foo] int $a, int $b)
    {
    }
```

## See Also

* [AnnotationToAttributeRector](https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#annotationtoattributerector)
