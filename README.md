# Rector Rules for Ray.Di
[![Continuous Integration](https://github.com/ray-di/rector-ray/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/ray-di/rector-ray/actions/workflows/continuous-integration.yml)

The [rector/rector](http://github.com/rectorphp/rector) rules for Ray.Di.

## Install

```bash
composer require ray/rector-ray 1.x-dev --dev
```

## Use Sets

```php
<?php
// rector.php
use Rector\Ray\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector;
use Rector\Core\Configuration\Option;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

require __DIR__ . '/vendor/autoload.php';

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::AUTO_IMPORT_NAMES, true);

    $services->set(RayDiNamedAnnotationRector::class);
};
```

See [Auto Import Names](https://github.com/rectorphp/rector/blob/main/docs/auto_import_names.md) for `Option::AUTO_IMPORT_NAME`.

## Rules

### RayDiNamedAnnotationRector

- class: [`RayDiNamedAnnotationRector`](rules/RayDiNamedAnnotation/Rector/ClassMethod/RayDiNamedAnnotationRector.php)

`@Named` annotation is converted to `#[Named]` attribute.

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

`Qualifier` are also converted.

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
