<?php

declare(strict_types=1);

/*
 * This file is part of YouTube Download for Contao.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

namespace DasL\ContaoYoutubeDlBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use DasL\ContaoYoutubeDlBundle\DasLContaoYoutubeDlBundle;
use DasL\YoutubeDlBundle\DasLYoutubeDlBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(DasLYoutubeDlBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
            BundleConfig::create(DasLContaoYoutubeDlBundle::class)
                ->setLoadAfter([DasLYoutubeDlBundle::class]),
        ];
    }
}
