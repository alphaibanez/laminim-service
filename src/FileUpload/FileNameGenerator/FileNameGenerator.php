<?php

namespace Lkt\FileUpload\FileNameGenerator;

use Lkt\FileUpload\FileUpload;

interface FileNameGenerator
{
    public function getFileName(string $source_name, string $type, string $tmp_name, int $index, array $content_range, FileUpload $upload): string;
}
