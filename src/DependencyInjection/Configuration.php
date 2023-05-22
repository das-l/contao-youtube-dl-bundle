<?php

declare(strict_types=1);

/*
 * This file is part of YouTube Download for Contao.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

namespace DasL\ContaoYoutubeDlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('das_l_contao_youtube_dl');
        $treeBuilder
            ->getRootNode()
            ->children()
                ->arrayNode('videoMaxHeights')
                    ->scalarPrototype()->end()
                    ->defaultValue([
                        '4096',
                        '2048',
                        '1080',
                        '720',
                        '480',
                        '360',
                    ])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
