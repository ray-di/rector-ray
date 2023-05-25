# Rector Rules for Ray.Di
[![Continuous Integration](https://github.com/ray-di/rector-ray/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/ray-di/rector-ray/actions/workflows/continuous-integration.yml)

The [rector/rector](http://github.com/rectorphp/rector) rules for [Ray.Di](https://ray-di.github.io/).

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
use Rector\Config\RectorConfig;
use Rector\Ray\AnnotationBinding\Rector\ClassMethod\AnnotationBindingRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AnnotationBindingRector::class);
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
