# YouTube Download for Contao

Download YouTube videos directly to the file system, using youtube-dl.

## Requirements

A `youtube-dl` or `yt-dlp` executable needs to be available. If the binary is located somewhere unusual, you can configure the location via the respective config parameter used by the `youtube-dl-bundle`:

```
das_l_youtube_dl:
    binPath: '/your/bin/directory/youtube-dl'
```

## Configuration

You can customize the available maximum video height options:

```
das_l_contao_youtube_dl:
    videoMaxHeights:
        - '1080'
        - '720'
        - '480'
```

## Usage

The extension adds a new icon button (a red play button with a downward-pointing green arrow) to every folder in the file system.

1. Click the button at the folder to which you want to download the video.
2. Provide the video ID.
3. Optionally choose a maximum video height to download.
4. Click "Import" and wait for the video to download.

That's all!

## Permissions

The extension adds a file operation permission "Import YouTube videos" in the "Filemounts" area of the user and user group settings.

## Please note

The downloader is limited to whatever the video creator uploaded and by what YouTube provides. The options you selected may possibly be discarded if no matching file could be found.

Also, this extension aims to have youtube-dl create an mp4 rather than an mkv container because mp4 has broader support. If this is causing you any problems, feel free to create an issue on GitHub so that we can find a solution that works well for everyone.
