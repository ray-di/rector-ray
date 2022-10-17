<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Ray\RayDiNamedAnnotation\Rector\ClassMethod\AnnotationBindingRector;

require __DIR__ . '/vendor/autoload.php';

return static function (RectorConfig $config): void {
    $services = $config->services();
    $services->set(AnnotationBindingRector::class);
};
