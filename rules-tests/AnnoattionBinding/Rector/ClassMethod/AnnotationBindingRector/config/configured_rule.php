<?php

declare(strict_types=1);

namespace RectorPrefix20220323;

use Rector\Config\RectorConfig;
use Rector\Ray\AnnotationBinding\Rector\ClassMethod\AnnotationBindingRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->rule(AnnotationBindingRector::class);
};
