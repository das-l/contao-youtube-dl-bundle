<?php

declare(strict_types=1);

/*
 * This file is part of YouTube Download for Contao.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

// Insert before drag button or at the end
(
    static function (&$array, $posKey, $insert): void {
        $pos = array_search($posKey, array_keys($array), true);

        if (false === $pos) {
            $pos = count($array);
        }

        $array = array_slice($array, 0, $pos)
            + $insert
            + array_slice($array, $pos)
        ;
    }
)(
    $GLOBALS['TL_DCA']['tl_files']['list']['operations'],
    'drag',
    [
        'importYoutube' => [
            'href' => 'key=importYoutube',
            'icon' => 'bundles/daslcontaoyoutubedl/youtube_dl.svg',
        ],
    ]
);
