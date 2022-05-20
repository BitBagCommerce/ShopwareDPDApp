<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ECSConfig $containerConfigurator): void {
    $containerConfigurator->import('vendor/sylius-labs/coding-standard/ecs.php');

    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/src',
    ]);
};
