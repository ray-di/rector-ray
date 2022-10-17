<?php

declare(strict_types=1);

namespace RectorPrefix20220323;

use Rector\Config\RectorConfig;
use Rector\Ray\AnnotationBinding\Rector\ClassMethod\AnnotationBindingRector;

return static function (RectorConfig $config): void {
    $services = $config->services();
    $services->set(AnnotationBindingRector::class);
};
