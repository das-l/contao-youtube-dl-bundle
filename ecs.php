<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Comment\HeaderCommentFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([__DIR__.'/vendor/contao/easy-coding-standard/config/contao.php']);

    $ecsConfig->ruleWithConfiguration(HeaderCommentFixer::class, [
        'header' => "This file is part of YouTube Download for Contao.\n\n(c) Das L – Alex Wuttke Software & Media\n\n@license LGPL-3.0-or-later"
    ]);
};
