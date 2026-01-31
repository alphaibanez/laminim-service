<?php

namespace Lkt\FileBrowser\Enums;

class FileEntityType
{
    const StorageUnit = 'unit';
    const Directory = 'dir';
    const Image = 'img';
    const Video = 'vid';
    const File = 'file';

    const Types = [
        self::StorageUnit,
        self::Directory,
        self::Image,
        self::Video,
        self::File,
    ];
}