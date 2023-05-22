<?php

declare(strict_types=1);

/*
 * This file is part of YouTube Download for Contao.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

namespace DasL\ContaoYoutubeDlBundle\Downloader;

use DasL\ContaoYoutubeDlBundle\Exception\DownloadErrorException;
use DasL\ContaoYoutubeDlBundle\Exception\InvalidOptionException;
use YoutubeDl\Options;
use YoutubeDl\YoutubeDl;

class YoutubeDownloader
{
    private $youtubeDl;

    public function __construct(YoutubeDl $youtubeDl)
    {
        $this->youtubeDl = $youtubeDl;
    }

    public function downloadVideo(string $downloadPath, string $url, array $additionalOptions = null): string
    {
        $videoPath = '';

        $options = Options::create()
            ->downloadPath($downloadPath)
            ->url($url)
        ;

        if (null !== $additionalOptions) {
            foreach ($additionalOptions as $optionName => $optionValue) {
                if (!method_exists($options, $optionName)) {
                    throw new InvalidOptionException($optionName);
                }

                $options = $options->$optionName($optionValue);
            }
        }
        $collection = $this->youtubeDl->download($options);

        foreach ($collection->getVideos() as $video) {
            if (null !== $video->getError()) {
                throw new DownloadErrorException($video->getError());
            }
            $videoPath = $video->getFileName();
        }

        return $videoPath;
    }
}
