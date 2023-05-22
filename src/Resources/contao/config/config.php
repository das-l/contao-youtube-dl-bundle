<?php

declare(strict_types=1);

/*
 * This file is part of YouTube Download for Contao.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

use DasL\ContaoYoutubeDlBundle\DataContainerAction\Files;

$GLOBALS['BE_MOD']['system']['files']['importYoutube'] = [Files::class, 'importYoutube'];
