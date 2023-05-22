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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DasLContaoYoutubeDlExtension extends Extension
{
    public function getAlias(): string
    {
        return 'das_l_contao_youtube_dl';
    }

    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration();
    }

    public function load(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $mergedConfig);
        $container->setParameter('das_l_contao_youtube_dl.videoMaxHeights', $config['videoMaxHeights']);
    }
}
