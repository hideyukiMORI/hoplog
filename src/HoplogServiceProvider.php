<?php

declare(strict_types=1);

namespace Hoplog;

use Hoplog\Beer\BeerServiceProvider;
use Hoplog\Brewery\BreweryServiceProvider;
use Hoplog\TastingNote\TastingNoteServiceProvider;
use Nene2\DependencyInjection\ContainerBuilder;
use Nene2\DependencyInjection\ServiceProviderInterface;

final readonly class HoplogServiceProvider implements ServiceProviderInterface
{
    public function register(ContainerBuilder $builder): void
    {
        $builder->addProvider(new BreweryServiceProvider());
        $builder->addProvider(new BeerServiceProvider());
        $builder->addProvider(new TastingNoteServiceProvider());
    }
}
