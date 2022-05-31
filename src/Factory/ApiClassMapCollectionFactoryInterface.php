<?php

declare(strict_types=1);

namespace BitBag\ShopwareDpdApp\Factory;

use Phpro\SoapClient\Soap\ClassMap\ClassMapCollection;

interface ApiClassMapCollectionFactoryInterface
{
    public function create(): ClassMapCollection;
}
