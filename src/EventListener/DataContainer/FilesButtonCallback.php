<?php

declare(strict_types=1);

/*
 * This file is part of YouTube Download for Contao.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

namespace DasL\ContaoYoutubeDlBundle\EventListener\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\Image;
use Contao\StringUtil;
use Symfony\Component\Security\Core\Security;

/**
 * @Callback(table="tl_files", target="list.operations.importYoutube.button")
 */
class FilesButtonCallback
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke($row, $href, $label, $title, $icon, $attributes): string
    {
        if ('folder' !== $row['type'] || !$this->security->isGranted('contao_user.fop', 'importYoutube')) {
            return '';
        }

        return '<a href="'.Controller::addToUrl($href.'&amp;id='.$row['id']).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
    }
}
