<?php

declare(strict_types=1);

/*
 * This file is part of YouTube Download for Contao.
 *
 * (c) Das L – Alex Wuttke Software & Media
 *
 * @license LGPL-3.0-or-later
 */

namespace DasL\ContaoYoutubeDlBundle\DataContainerAction;

use Contao\BackendTemplate;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\Exception\RedirectResponseException;
use Contao\Message;
use DasL\ContaoYoutubeDlBundle\Downloader\YoutubeDownloader;
use DasL\ContaoYoutubeDlBundle\Exception\DownloadErrorException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class Files
{
    private $youtubeDownloader;
    private $security;
    private $session;
    private $requestStack;
    private $translator;
    private $projectDir;
    private $contaoUploadPath;
    private $ffmpegPath;
    private $videoMaxHeights;

    public function __construct(YoutubeDownloader $youtubeDownloader, Security $security, SessionInterface $session, RequestStack $requestStack, TranslatorInterface $translator, string $projectDir, string $contaoUploadPath, ?string $ffmpegPath, array $videoMaxHeights)
    {
        $this->youtubeDownloader = $youtubeDownloader;
        $this->security = $security;
        $this->session = $session;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
        $this->projectDir = $projectDir;
        $this->contaoUploadPath = $contaoUploadPath;
        $this->ffmpegPath = $ffmpegPath;
        $this->videoMaxHeights = $videoMaxHeights;
    }

    public function importYoutube(): string
    {
        $folder = $this->req()->query->get('id');
        $targetDir = $this->projectDir.'/'.$folder;

        if (!$this->security->isGranted('contao_user.fop', 'importYoutube')) {
            throw new AccessDeniedException('No permission to import YouTube videos.');
        }

        if (!$this->security->isGranted('contao_user.filemounts', $folder)) {
            throw new AccessDeniedException('Target directory is not an allowed filemount for this user.');
        }

        if ('importYoutube' !== $this->req()->query->get('key') || !is_dir($targetDir)) {
            return '';
        }

        if ($this->getFormId() === $this->req()->request->get('FORM_SUBMIT')) {
            $this->handleSubmit($targetDir);
        }

        return $this->renderForm();
    }

    protected function handleSubmit($targetDir): void
    {
        if (0 !== strpos(realpath($targetDir).'/', realpath($this->projectDir.'/'.$this->contaoUploadPath).'/')) {
            throw new AccessDeniedException('Target directory outside upload path.');
        }

        $uri = $this->req()->getRequestUri();
        $youtubeString = $this->req()->request->get('youtubeId') ?: '';
        $maxHeight = $this->req()->request->get('videoMaxHeight') ?: '';

        $additionalOptions = [];

        if (empty($youtubeString)) {
            Message::addError(
                $this->translator->trans(
                    'ERR.mandatory',
                    [$this->translator->trans('tl_content.youtube.0', [], 'contao_tl_content')],
                    'contao_default'
                )
            );

            throw new RedirectResponseException($uri, 303);
        }

        $additionalOptions['format'] = 'bestvideo[ext=mp4]+bestaudio[ext=m4a]/best';

        if (!empty($maxHeight)) {
            if (!\in_array($maxHeight, $this->getMaxHeights(), true)) {
                throw new \UnexpectedValueException('Unsupported max height.');
            }

            $this->session->set('das_l_contao_youtube_dl.videoMaxHeight', $maxHeight);

            $additionalOptions['format'] = sprintf('bestvideo[ext=mp4][height<=%1$s]+bestaudio[ext=m4a]/best[height<=%1$s]', $maxHeight);
        }

        if ($this->ffmpegPath) {
            $additionalOptions['ffmpegLocation'] = $this->ffmpegPath;
        }

        $youtubeId = $this->extractYoutubeId($youtubeString);

        if (!preg_match('/^[a-zA-Z0-9_-]{11}$/', $youtubeId)) {
            Message::addError(
                $this->translator->trans(
                    'ERR.youtubeIdInvalid',
                    [htmlspecialchars($youtubeId)],
                    'contao_default'
                )
            );

            throw new RedirectResponseException($uri, 303);
        }

        try {
            $this->doImport($targetDir, 'https://www.youtube.com/watch?v='.urlencode($youtubeId), $additionalOptions);
            Message::addConfirmation($this->translator->trans('MSC.youtubeDlSuccess', [], 'contao_default'));
        } catch (DownloadErrorException $dee) {
            Message::addError(
                $this->translator->trans(
                    'ERR.youtubeDlError',
                    [htmlspecialchars($youtubeId), htmlspecialchars($dee->getMessage())],
                    'contao_default'
                )
            );

            throw new RedirectResponseException($uri, 303);
        }
    }

    protected function extractYoutubeId($youtubeString): string
    {
        // Cf. contao/core-bundle tl_content
        if (
            preg_match(
                '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
                $youtubeString,
                $matches = []
            )
        ) {
            $youtubeString = $matches[1];
        }

        return $youtubeString;
    }

    protected function doImport($targetDir, $url, $additionalOptions): string
    {
        return $this->youtubeDownloader->downloadVideo($targetDir, $url, $additionalOptions);
    }

    protected function renderForm(): string
    {
        $template = new BackendTemplate('be_files_importYoutube');

        $template->backUrl = str_replace(
            ['&key='.$this->req()->query->get('key'), '&id='.urlencode($this->req()->query->get('id'))],
            '',
            $this->req()->getRequestUri()
        );
        $template->backBT = $this->translator->trans('MSC.backBT', [], 'contao_default');
        $template->backBTTitle = $this->translator->trans('MSC.backBTTitle', [], 'contao_default');
        $template->formId = $this->getFormId();

        $template->youtubeIdLabel = $this->translator->trans('tl_content.youtube.0', [], 'contao_tl_content');
        $template->youtubeIdHelp = $this->translator->trans('tl_content.youtube.1', [], 'contao_tl_content');

        $maxHeight = $this->session->has('das_l_contao_youtube_dl.videoMaxHeight')
            ? $this->session->get('das_l_contao_youtube_dl.videoMaxHeight')
            : ''
        ;
        $this->session->set('das_l_contao_youtube_dl.videoMaxHeight', '');
        $template->videoMaxHeights = $this->getMaxHeights();
        $template->videoMaxHeightSelected = \in_array($maxHeight, $template->videoMaxHeights, true) ? $maxHeight : '';
        $template->videoMaxHeightLabel = $this->translator->trans('MSC.youtubeVideoMaxHeight.0', [], 'contao_default');
        $template->videoMaxHeightHelp = $this->translator->trans('MSC.youtubeVideoMaxHeight.1', [], 'contao_default');

        $template->submitLabel = $this->translator->trans('MSC.youtubeSubmitImport', [], 'contao_default');

        return $template->parse();
    }

    private function getFormId()
    {
        return 'tl_files_importYoutube';
    }

    private function getMaxHeights()
    {
        return $this->videoMaxHeights;
    }

    private function req()
    {
        return $this->requestStack->getCurrentRequest();
    }
}
